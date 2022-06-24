<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\safeLazy;

/** @covers ::j45l\maybe\Optional\safeLazy */
class SafeLazyTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        assertNone(safeLazy(None::create())());
    }

    public function testsOnNullReturnsNone(): void
    {
        assertNone(safeLazy(null)());
    }

    public function testsOnFailingCallableReturnsFailure(): void
    {
        $fail = function () {
            throw new RuntimeException();
        };

        assertFailure(safeLazy($fail)());
    }

    public function testsOnNullReturningCallableReturnsNone(): void
    {
        $nullReturning = function () {
            return null;
        };

        assertNone(safeLazy($nullReturning)());
    }

    public function testsOnValueReturningCallableReturnsNone(): void
    {
        $valueReturning = function () {
            return 42;
        };

        assertSomeEquals(42, safeLazy($valueReturning)());
    }

    public function testsOnValueSomeReturnsSome(): void
    {
        assertSomeEquals(42, safeLazy(Some::from(42))());
    }

    public function testsOnValueReturnsSome(): void
    {
        assertSomeEquals(42, safeLazy(42)());
    }
}
