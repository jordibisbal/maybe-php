<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\safeLazy;

/** @covers ::j45l\maybe\Optional\safeLazy */
class SafeLazyTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safeLazy(None::create())());
    }

    public function testsOnNullReturnsNone(): void
    {
        self::assertInstanceOf(None::class, safeLazy(null)());
    }

    public function testsOnFailingCallableReturnsFailure(): void
    {
        $fail = function () {
            throw new RuntimeException();
        };

        self::assertInstanceOf(Failure::class, safeLazy($fail)());
    }

    public function testsOnNullReturningCallableReturnsNone(): void
    {
        $nullReturning = function () {
            return null;
        };

        self::assertInstanceOf(None::class, safeLazy($nullReturning)());
    }

    public function testsOnValueReturningCallableReturnsNone(): void
    {
        $valueReturning = function () {
            return 42;
        };

        self::assertInstanceOf(Some::class, safeLazy($valueReturning)());
    }

    public function testsOnValueSomeReturnsSome(): void
    {
        $some = safeLazy(Some::from(42))();
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnValueReturnsSome(): void
    {
        $some = safeLazy(42)();
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }
}
