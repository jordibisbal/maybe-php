<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Success;
use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\Maybe;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Test\Unit\LeftRightExamplesFixtures\EntityManagerStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\functional\apply;
use function j45l\maybe\Functions\getFailureReasons;
use function j45l\maybe\Functions\getSomes;
use function j45l\maybe\Functions\safeMap;

/**
 * @coversNothing
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LeftRightExamplesTest extends TestCase
{
    public function testDo(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();

        $maybe =
            LeftRight::do($this->insertCustomer($entityManager)($customer))
            ->orElse(apply($this->updateCustomer($entityManager), $customer))
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(None::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Success::class, $maybe);
    }

    /** @param EntityManagerStub<string> $entityManager */
    private function insertCustomer(EntityManagerStub $entityManager): Closure
    {
        return static function ($customer) use ($entityManager): JustSuccess {
            $entityManager->insert($customer);
            return JustSuccess::create();
        };
    }

    /** @param EntityManagerStub<string> $entityManager */
    private function updateCustomer(EntityManagerStub $entityManager): Closure
    {
        return static function ($customer) use ($entityManager): JustSuccess {
            $entityManager->update($customer);
            return JustSuccess::create();
        };
    }

    public function testDoOrElse(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;

        $maybe =
            LeftRight::do($this->insertCustomer($entityManager), $customer)
                ->orElse(apply($this->updateCustomer($entityManager), $customer))
        ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Success::class, $maybe);
    }

    public function testDoOrElseFails(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;
        $entityManager->updateWillFail = true;

        $maybe =
            LeftRight::do($this->insertCustomer($entityManager), $customer)
                ->orElse(apply($this->updateCustomer($entityManager), $customer))
            ;

        $this->assertInstanceOf(Some::class, $entityManager->insertInvokedWith);
        $this->assertInstanceOf(Some::class, $entityManager->updateInvokedWith);
        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        $this->assertInstanceOf(Failure::class, $maybe);
    }

    public function testMap(): void
    {
        $sideEffect = false;
        $increment = function (Some $number) use (&$sideEffect): Some {
            $sideEffect = true;
            return Some::from($number->get() + 1);
        };

        $maybe = Some::from(41)->map($increment);
        $this->assertTrue($sideEffect);

        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
    }

    public function testSafeMap(): void
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

        $all = safeMap([
            'id' => $id,
            'name' => $name,
            'email' => $email
        ])();

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], getSomes($all));
        $this->assertEquals(['name' => 'Unable to get name'], getFailureReasons($all));
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

    public function testAndThen(): void
    {
        $increment = /** @param Some<int> $some */ function (Some $some): int {
            return $some->get() + 1;
        };

        $pipe = Some::from(41)->andThen($increment)->andThen($increment);

        $this->assertInstanceOf(Some::class, $pipe);
        $this->assertEquals(43, $pipe->get());
    }

    public function testAndThenFromNone(): void
    {
        $called = false;
        $increment = function () use (&$called) {
            $called = true;
        };
        $failure = function () {
            throw new RuntimeException();
        };

        $pipe = Some::from(42)->andThen($failure)->andThen($increment)->andThen($increment);

        $this->assertInstanceOf(Failure::class, $pipe);
        $this->assertFalse($called);
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

        $failure = Maybe::do($failure)->andThen($increment);

        $this->assertInstanceOf(Failure::class, $failure);
        $this->assertEquals('42!', $failure->reason()->toString());
        $this->assertFalse($called);
    }
}
