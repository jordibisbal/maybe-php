<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\safe;

/** @covers ::j45l\maybe\Optional\safe */
class SafeTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        assertNone(safe(None::create()));
    }

    public function testsOnNullReturnsNone(): void
    {
        assertNone(safe(null));
    }

    public function testsOnFailingCallableReturnsFailure(): void
    {
        $fail = function () {
            throw new RuntimeException();
        };

        assertFailure(safe($fail));
    }

    public function testsOnNullReturningCallableReturnsNone(): void
    {
        $nullReturning = function () {
            return null;
        };

        assertNone(safe($nullReturning));
    }

    public function testsOnValueReturningCallableReturnsNone(): void
    {
        $valueReturning = function () {
            return 42;
        };

        assertSomeEquals(42, safe($valueReturning));
    }

    public function testsOnValueSomeReturnsSome(): void
    {
        $some = safe(Some::from(42));

        assertSomeEquals(42, $some);
    }

    public function testsOnValueReturnsSome(): void
    {
        $some = safe(42);

        assertSomeEquals(42, $some);
    }
}
