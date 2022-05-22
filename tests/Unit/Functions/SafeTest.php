<?php

namespace j45l\maybe\Test\Unit\Functions;

use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Functions\safe;

/** @covers ::j45l\maybe\Functions\safe */
class SafeTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safe(None::create())());
    }

    public function testsOnNullReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safe(null)());
    }

    public function testsOnFailingCallableReturnsFailure(): void
    {
        $fail = function () {
            throw new RuntimeException();
        };

        self::assertInstanceOf(Failure::class, safe($fail)());
    }

    public function testsOnNullReturningCallableReturnsNone(): void
    {
        $nullReturning = function () {
            return null;
        };

        self::assertInstanceOf(None::class, safe($nullReturning)());
    }

    public function testsOnValueReturningCallableReturnsNone(): void
    {
        $valueReturning = function () {
            return 42;
        };

        self::assertInstanceOf(Some::class, safe($valueReturning)());
    }

    public function testsOnValueSomeReturnsSome(): void
    {
        $some = safe(Some::from(42))();
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnValueReturnsSome(): void
    {
        $some = safe(42)();
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }
}
