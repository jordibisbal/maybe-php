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

/** @coversNothing */
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

    /**
     * @param EntityManagerStub $entityManager
     * @return Closure
     */
    private function insertCustomer(EntityManagerStub $entityManager): Closure
    {
        return static function ($customer) use ($entityManager): Success {
            $entityManager->insert($customer);
            return Success::create();
        };
    }

    /**
     * @param EntityManagerStub $entityManager
     * @return Closure
     */
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
            return Some::from($some->value() + 1);
        };

        $either = Some::from(42)
            ->pipe($increment)
            ->pipe($increment)
        ;

        $firstContextParameter = first($either->context()->parameters()->asArray());

        $this->assertInstanceOf(Some::class, $firstContextParameter);
        $this->assertEquals(43, $firstContextParameter->value());

        $this->assertCount(2, $either->context()->trail());
        $this->assertEquals([42, 43], $either->context()->trail()->getValues());
        $this->assertEquals([42, 43, 44], $either->trail()->getValues());
    }

    public function testMap(): void
    {
        $sideEffect = false;
        $increment = function (Some $number) use (&$sideEffect) {
            $sideEffect = true;
            return Some::from($number->value() + 1);
        };

        $either = Some::from(41)->map($increment);
        $this->assertFalse($sideEffect);

        $this->assertInstanceOf(Deferred::class, $either);

        $either = $either->resolve();
        $this->assertTrue($sideEffect);

        $this->assertInstanceOf(Some::class, $either);
        $this->assertEquals(42, $either->value());
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

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $trail->getTaggedValues());
        $this->assertEquals(
            ['name' => 'Unable to get name'],
            invoke($trail->getTaggedFailureReasons(), 'asString')
        );
    }
}
