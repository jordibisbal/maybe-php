<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\DoTry\ThrowableReason;
use j45l\maybe\Some;
use j45l\maybe\Test\Unit\Fixtures\EntityManagerStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function Functional\first;
use function Functional\invoke;

/**
 * @coversNothing
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ExamplesTest extends TestCase
{
    public function testDo(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();

        $maybe =
            Maybe::start()->next($this->insertCustomer($entityManager))->with($customer)
            ->orElse($this->updateCustomer($entityManager))
            ->resolve()
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(None::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Success::class, $maybe);
        $this->assertCount(0, $maybe->trail()->failed());
    }

    /** @param EntityManagerStub<string> $entityManager */
    private function insertCustomer(EntityManagerStub $entityManager): Closure
    {
        return static function ($customer) use ($entityManager): Success {
            $entityManager->insert($customer);
            return Success::create();
        };
    }

    /** @param EntityManagerStub<string> $entityManager */
    private function updateCustomer(EntityManagerStub $entityManager): Closure
    {
        return static function ($customer) use ($entityManager): Success {
            $entityManager->update($customer);
            return Success::create();
        };
    }

    public function testDoOrElse(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;

        $maybe =
            Maybe::start()->next($this->insertCustomer($entityManager))->with($customer)
                ->orElse($this->updateCustomer($entityManager))
                ->resolve()
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Success::class, $maybe);
        $this->assertCount(0, $maybe->trail()->failed());
    }

    public function testDoOrElseFails(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;
        $entityManager->updateWillFail = true;

        $maybe =
            Maybe::start()->next($this->insertCustomer($entityManager))->with($customer)
                ->orElse($this->updateCustomer($entityManager))
                ->resolve();

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Failure::class, $maybe);
        $this->assertCount(1, $maybe->trail()->failed());

        $reason = $maybe->trail()->failed()[0]->reason();
        $this->assertInstanceOf(ThrowableReason::class, $reason);
        $this->assertEquals(new RuntimeException('Failed to update'), $reason->throwable());
        $this->assertEquals($reason, $maybe->reason());
    }

    public function testGetContext(): void
    {
        $increment = static function (Some $some): Some {
            return Some::from($some->get() + 1);
        };

        $maybe = Some::from(42)
            ->pipe($increment)
            ->pipe($increment)
        ;

        $firstContextParameter = first($maybe->context()->parameters()->asArray());

        $this->assertInstanceOf(Some::class, $firstContextParameter);
        $this->assertEquals(43, $firstContextParameter->get());

        $this->assertCount(2, $maybe->context()->trail());
        $this->assertEquals([42, 43], $maybe->context()->trail()->values());
        $this->assertEquals([42, 43, 44], $maybe->trail()->values());
    }

    public function testMap(): void
    {
        $sideEffect = false;
        $increment = function (Some $number) use (&$sideEffect) {
            $sideEffect = true;
            return Some::from($number->get() + 1);
        };

        $maybe = Some::from(41)->map($increment);
        $this->assertTrue($sideEffect);

        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
    }

    public function testTag(): void
    {
        $name = static function (): void {
            throw new RuntimeException('Unable to get name');
        };
        $id = static function (): int {
            return 123;
        };
        $email = static function (): string {
            return 'email@test.com';
        };

        $chain = Maybe::start()
            ->withTag('id')->next($id)
            ->withTag('name')->next($name)
            ->withTag('email')->next($email)
        ;

        $trail = $chain->trail();

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $trail->taggedValues());
        $this->assertEquals(
            ['name' => 'Unable to get name'],
            invoke($trail->taggedFailureReasons(), 'toString')
        );
    }

    public function testNextTag(): void
    {
        $name = static function (): void {
            throw new RuntimeException('Unable to get name');
        };
        $id = static function (): int {
            return 123;
        };
        $email = static function (): string {
            return 'email@test.com';
        };


        $chain = Maybe::start()
            ->tagNext('id', $id)
            ->tagNext('name', $name)
            ->tagNext('email', $email)
        ;

        $trail = $chain->trail();

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $trail->taggedValues());
        $this->assertEquals(
            ['name' => 'Unable to get name'],
            invoke($trail->taggedFailureReasons(), 'toString')
        );
    }

    public function testNext(): void
    {
        $fortyTwo = function (): int {
            return 42;
        };
        $failure = function (): void {
            throw new RuntimeException('42!');
        };

        $noneNext = None::create()->next(42);
        $someNext = Some::from(1)->next(42)->next($failure)->next($fortyTwo)->resolve();

        $this->assertInstanceOf(Some::class, $noneNext);
        $this->assertInstanceOf(Some::class, $someNext);
        $this->assertEquals(42, $noneNext->get());
        $this->assertEquals(42, $someNext->get());
        $this->assertEquals([1, 42, 42], $someNext->trail()->values());
        $this->assertEquals([ThrowableReason::fromString('42!')], $someNext->trail()->failureReasons());
    }

    public function testOrElse(): void
    {
        $noneNext = None::create()->orElse(42);
        $someNext = Some::from(1)->orElse(42);

        $this->assertInstanceOf(Some::class, $noneNext);
        $this->assertInstanceOf(Some::class, $someNext);
        $this->assertEquals(42, $noneNext->get());
        $this->assertEquals(1, $someNext->get());
    }

    public function testPipe(): void
    {
        $increment = /** @param Some<int> $some */ function (Some $some): int {
            return $some->get() + 1;
        };

        $pipe = Some::from(42)->pipe($increment)->pipe($increment)->pipe($increment)->resolve();

        $this->assertInstanceOf(Some::class, $pipe);
        $this->assertEquals(45, $pipe->get());
    }

    public function testPipeWithNone(): void
    {
        $called = false;
        $increment = function () use (&$called) {
            $called = true;
        };
        $failure = function () {
            throw new RuntimeException();
        };

        $pipe = Some::from(42)->pipe($failure)->pipe($increment)->pipe($increment)->resolve();

        $this->assertInstanceOf(Failure::class, $pipe);
        $this->assertFalse($called);
    }

    public function testResolve(): void
    {
        $closure = function (): int {
            return 42;
        };
        $deferred = Deferred::create($closure);

        $this->assertInstanceOf(Deferred::class, $deferred);
        $deferred = $deferred->resolve();
        $this->assertInstanceOf(Some::class, $deferred);
        $this->assertEquals(42, $deferred->get());
    }

    public function testFailingResolve(): void
    {
        $closure = function (): int {
            throw new RuntimeException('42!');
        };
        $deferred = Deferred::create($closure);

        $this->assertInstanceOf(Deferred::class, $deferred);
        $deferred = $deferred->resolve();
        $this->assertInstanceOf(Failure::class, $deferred);
        $this->assertEquals('42!', $deferred->reason()->toString());
    }

    public function testThen(): void
    {
        $increment = function (int $value): int {
            return $value + 1;
        };

        $maybe = Maybe::start()->with(41)->andThen($increment);

        $this->assertInstanceOf(Deferred::class, $maybe);

        $some = $maybe->resolve();

        $this->assertInstanceOf(Some::class, $some);
        $this->assertEquals(42, $some->get());
    }

    public function testThenWithNone(): void
    {
        $called = false;
        $increment = function () use (&$called) {
            $called = true;
        };
        $failure = function () {
            throw new RuntimeException('42!');
        };

        $failure = Maybe::start()->andThen($failure)->andThen($increment);

        $this->assertInstanceOf(Failure::class, $failure);
        $this->assertEquals('42!', $failure->reason()->toString());
        $this->assertFalse($called);
    }
}
