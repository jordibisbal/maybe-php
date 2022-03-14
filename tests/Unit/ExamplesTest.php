<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use Closure;
use j45l\either\Deferred;
use j45l\either\Either;
use j45l\either\Failure;
use j45l\either\None;
use j45l\either\Some;
use j45l\either\Success;
use j45l\either\Test\Unit\Stubs\EntityManagerStub;
use j45l\either\ThrowableReason;
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

        $either =
            Either::start()->next($this->insertCustomer($entityManager))->with($customer)
            ->orElse($this->updateCustomer($entityManager))
            ->resolve()
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(None::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Success::class, $either);
        $this->assertCount(0, $either->trail()->failed());
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

        $either =
            Either::start()->next($this->insertCustomer($entityManager))->with($customer)
                ->orElse($this->updateCustomer($entityManager))
                ->resolve()
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Success::class, $either);
        $this->assertCount(0, $either->trail()->failed());
    }

    public function testDoOrElseFails(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;
        $entityManager->updateWillFail = true;

        $either =
            Either::start()->next($this->insertCustomer($entityManager))->with($customer)
                ->orElse($this->updateCustomer($entityManager))
                ->resolve();

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Failure::class, $either);
        $this->assertCount(1, $either->trail()->failed());

        $reason = $either->trail()->failed()[0]->reason();
        $this->assertInstanceOf(ThrowableReason::class, $reason);
        $this->assertEquals(new RuntimeException('Failed to update'), $reason->throwable());
        $this->assertEquals($reason, $either->reason());
    }

    public function testGetContext(): void
    {
        $increment = static function (Some $some): Some {
            return Some::from($some->get() + 1);
        };

        $either = Some::from(42)
            ->pipe($increment)
            ->pipe($increment)
        ;

        $firstContextParameter = first($either->context()->parameters()->asArray());

        $this->assertInstanceOf(Some::class, $firstContextParameter);
        $this->assertEquals(43, $firstContextParameter->get());

        $this->assertCount(2, $either->context()->trail());
        $this->assertEquals([42, 43], $either->context()->trail()->values());
        $this->assertEquals([42, 43, 44], $either->trail()->values());
    }

    public function testMap(): void
    {
        $sideEffect = false;
        $increment = function (Some $number) use (&$sideEffect) {
            $sideEffect = true;
            return Some::from($number->get() + 1);
        };

        $either = Some::from(41)->map($increment);
        $this->assertFalse($sideEffect);

        $this->assertInstanceOf(Deferred::class, $either);

        $either = $either->resolve();
        $this->assertTrue($sideEffect);

        $this->assertInstanceOf(Some::class, $either);
        $this->assertEquals(42, $either->get());
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

        $chain = Either::start()
            ->withTag('id')->next($id)
            ->withTag('name')->next($name)
            ->withTag('email')->next($email)
        ;

        $trail = $chain->trail();

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $trail->taggedValues());
        $this->assertEquals(
            ['name' => 'Unable to get name'],
            invoke($trail->taggedFailureReasons(), 'asString')
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


        $chain = Either::start()
            ->tagNext('id', $id)
            ->tagNext('name', $name)
            ->tagNext('email', $email)
        ;

        $trail = $chain->trail();

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $trail->taggedValues());
        $this->assertEquals(
            ['name' => 'Unable to get name'],
            invoke($trail->taggedFailureReasons(), 'asString')
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
        $this->assertEquals([ThrowableReason::from('42!')], $someNext->trail()->failureReasons());
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
        $this->assertEquals('42!', $deferred->reason()->asString());
    }

    public function testThen(): void
    {
        $increment = function (int $value): int {
            return $value + 1;
        };

        $either = Either::start()->with(41)->then($increment);

        $this->assertInstanceOf(Deferred::class, $either);

        $some = $either->resolve();

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

        $failure = Either::start()->then($failure)->then($increment);

        $this->assertInstanceOf(Failure::class, $failure);
        $this->assertEquals('42!', $failure->reason()->asString());
        $this->assertFalse($called);
    }
}
