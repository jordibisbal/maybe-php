<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\Maybe
 */
class DoTest extends TestCase
{
    public function testValueReturningCallableResultsInASuccess(): void
    {
        $success = Either::do(function (): int {
            return 42;
        });

        self::assertInstanceOf(Success::class, $success);
        self::assertInstanceOf(Some::class, $success);
        self::assertEquals(42, $success->get());
    }

    public function testThrowingCallableResultInAFailure(): void
    {
        $failure = Either::do(function (): void {
            throw new RuntimeException('Runtime exception');
        });

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals('Runtime exception', $failure->reason()->toString());
    }

    public function testNoneReturningCallableResultsSomeIsSuccess(): void
    {
        $some = Either::do(function (): Some {
            return Some::from(42);
        });

        self::assertInstanceOf(Some::class, $some);
        self::assertInstanceOf(Success::class, $some);
    }

    public function testNoneReturningCallableResultsNoneIsNotASuccess(): void
    {
        $none = Either::do(function (): None {
            return None::create();
        });

        self::assertInstanceOf(None::class, $none);
        self::assertNotInstanceOf(Success::class, $none);
    }

    public function testNoneReturningCallableResultsNullIsNotASuccess(): void
    {
        $none = Either::do(function () {
            return null;
        });

        self::assertInstanceOf(None::class, $none);
        self::assertNotInstanceOf(Success::class, $none);
    }
}
