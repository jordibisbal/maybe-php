<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\Maybe;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Test\Unit\Fixtures\EntityManagerStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function Functional\map;
use function j45l\functional\apply;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;
use function j45l\maybe\Optional\safe;
use function j45l\maybe\Optional\safeAll;

/**
 * @coversNothing
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OptionalExamplesTest extends TestCase
{
    public function testDo(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();

        $upsert =
            Optional::do($this->insertCustomer($entityManager)($customer))
            ->orElse(apply($this->updateCustomer($entityManager), $customer))
        ;

        self::assertEquals($customer, $entityManager->insertInvokedWith);
        assertNone($entityManager->updateInvokedWith);
        assertSuccess($upsert);
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

        $upsert =
            Optional::do($this->insertCustomer($entityManager), $customer)
                ->orElse(apply($this->updateCustomer($entityManager), $customer))
        ;

        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        assertSuccess($upsert);
    }

    public function testDoOrElseFails(): void
    {
        $customer = Some::from('customer');
        $entityManager = new EntityManagerStub();
        $entityManager->insertWillFail = true;
        $entityManager->updateWillFail = true;

        $upsert =
            Optional::do($this->insertCustomer($entityManager), $customer)
                ->orElse(apply($this->updateCustomer($entityManager), $customer))
            ;

        $this->assertEquals($customer, $entityManager->insertInvokedWith);
        $this->assertEquals($customer, $entityManager->updateInvokedWith);
        assertFailure($upsert);
    }

    public function testMap(): void
    {
        $sideEffect = false;
        $increment = function ($number) use (&$sideEffect) {
            $sideEffect = true;

            return $number + 1;
        };

        $maybe = Some::from(41)->map($increment);

        $this->assertTrue($sideEffect);
        assertSomeEquals(42, $maybe);
    }

    public function testMapArray(): void
    {
        $optionalArray = map([null, 0, 1, 2], function ($x) {
            return safe($x);
        });

        [$none, $failure, $one, $half] = map(
            $optionalArray,
            function (Optional $x) {
                return $x->map(static function ($x) {
                    return 1 / $x;
                });
            }
        );

        assertNone($none);
        assertFailure($failure);
        assertSomeEquals(1, $one);
        assertSomeEquals(1 / 2, $half);
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

        $all = safeAll([
            'id' => $id,
            'name' => $name,
            'email' => $email
        ]);

        $this->assertEquals(['id' => 123, 'email' => 'email@test.com'], $all->values());
        $this->assertEquals(['name' => 'Unable to get name'], $all->failureReasonStrings());
    }

    public function testOrElse(): void
    {
        $noneNext = None::create()->orElse(42);
        $someNext = Some::from(1)->orElse(42);

        assertSomeEquals(42, $noneNext);
        assertSomeEquals(1, $someNext);
    }

    public function testAndThen(): void
    {
        $increment = /** @param Some<int> $some */ function (Some $some): int {
            return $some->get() + 1;
        };

        $pipe = Some::from(41)->andThen($increment)->andThen($increment);

        assertSomeEquals(43, $pipe);
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

        assertFailure($pipe);
        $this->assertFalse($called);
    }

    public function testThenWithNone(): void
    {
        $called = false;
        $increment = function () use (&$called) {
            $called = true;
        };
        $failure = static function () {
            throw new RuntimeException('42!');
        };

        $failure = Maybe::do($failure)->andThen($increment);

        assertFailureReasonString('42!', $failure);
        $this->assertFalse($called);
    }
}
