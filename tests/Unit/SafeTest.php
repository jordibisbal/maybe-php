<?php

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Deferred;
use j45l\maybe\None;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\safe;

/** @covers ::j45l\maybe\safe */
class SafeTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safe(None::create()));
    }

    public function testsOnNullReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safe(null));
    }

    public function testsOnFailingCallableReturnsNone(): void
    {
        $fail = function () {
            throw new RuntimeException();
        };

        self::assertInstanceOf(None::class, safe($fail));
    }

    public function testsOnNullReturningCallableReturnsNone(): void
    {
        $nullReturning = function () {
            return null;
        };

        self::assertInstanceOf(None::class, safe($nullReturning));
    }

    public function testsOnValueReturningCallableReturnsNone(): void
    {
        $valueReturning = function () {
            return 42;
        };

        self::assertInstanceOf(Some::class, safe($valueReturning));
    }

    public function testsOnNullSomeReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safe(Some::from(null)));
    }

    public function testsOnValueSomeReturnsSome(): void
    {
        $some = safe(Some::from(42));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnValueReturnsSome(): void
    {
        $some = safe(42);
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnDeferredValueReturnsSome(): void
    {
        $some = safe(Deferred::create(function () {
            return 42;
        }));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnDeferredNullReturnsNone(): void
    {
        $some = safe(Deferred::create(function () {
            return 42;
        }));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }
}
