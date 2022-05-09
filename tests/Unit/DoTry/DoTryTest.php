<?php

namespace j45l\maybe\Test\Unit\DoTry;

use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\None;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\DoTry\doTry;

/** @covers ::j45l\maybe\DoTry\doTry */
class DoTryTest extends TestCase
{
    public function testValueReturningCallableResultsInASuccess(): void
    {
        $success = doTry(function (): int {
            return 42;
        });

        self::assertInstanceOf(Success::class, $success);
        self::assertEquals(42, $success->get());
    }

    public function testThrowingCallableResultInAFailure(): void
    {
        $failure = doTry(function (): void {
            throw new RuntimeException('Runtime exception');
        });

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals('Runtime exception', $failure->reason()->toString());
    }

    public function testNoneReturningCallableResultsInASuccess(): void
    {
        $none = doTry(function (): None {
            return None::create();
        });

        self::assertInstanceOf(Success::class, $none);
    }
}
