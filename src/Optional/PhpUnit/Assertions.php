<?php

declare(strict_types=1);

namespace j45l\maybe\Optional\PhpUnit;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Success;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;

use function function_exists;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotInstanceOf;

if (!function_exists('j45l\maybe\Optional\assertNone')) {
    /** @param mixed $actual */
    function assertNone($actual): void
    {
        assertInstanceOf(None::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertFailure')) {
    /** @param mixed $actual */
    function assertFailure($actual): void
    {
        assertInstanceOf(Failure::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertSome')) {
    /** @param mixed $actual */
    function assertSome($actual): void
    {
        assertInstanceOf(Some::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertSomeEquals')) {
    /**
     * @param mixed $actual
     * @param mixed $expected
     */
    function assertSomeEquals($expected, $actual): void
    {
        assertInstanceOf(Some::class, $actual);
        assertEquals($expected, $actual->get());
    }
}

if (!function_exists('j45l\maybe\Optional\assertSuccess')) {
    /** @param mixed $actual */
    function assertSuccess($actual): void
    {
        assertInstanceOf(Success::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertNotSuccess')) {
    /** @param mixed $actual */
    function assertNotSuccess($actual): void
    {
        assertNotInstanceOf(Success::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertNotFailure')) {
    /** @param mixed $actual */
    function assertNotFailure($actual): void
    {
        assertNotInstanceOf(Failure::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertJustSuccess')) {
    /** @param mixed $actual */
    function assertJustSuccess($actual): void
    {
        assertInstanceOf(JustSuccess::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\assertFailureReasonString')) {
    /**
     * @param mixed $actual
     */
    function assertFailureReasonString(string $expected, $actual): void
    {
        assertInstanceOf(Failure::class, $actual);
        assertEquals($expected, $actual->reason()->toString());
    }
}

if (!function_exists('j45l\maybe\Optional\assertFailureReasonThrowable')) {
    /**
     * @param class-string $expected
     * @param mixed $actual
     */
    function assertFailureReasonThrowable(string $expected, $actual): void
    {
        assertInstanceOf(Failure::class, $actual);
        assertInstanceOf(ThrowableReason::class, $actual->reason());
        assertInstanceOf($expected, $actual->reason()->throwable());
    }
}
