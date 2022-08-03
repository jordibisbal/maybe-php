<?php

declare(strict_types=1);

namespace j45l\maybe\Optional\PhpUnit;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\Assert;
use function function_exists;

if (!function_exists('j45l\maybe\Optional\Phpunit\assertNone')) {
    /** @param mixed $actual */
    function assertNone($actual): void
    {
        Assert::assertInstanceOf(None::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertFailure')) {
    /** @param mixed $actual */
    function assertFailure($actual): void
    {
        Assert::assertInstanceOf(Failure::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertSome')) {
    /** @param mixed $actual */
    function assertSome($actual): void
    {
        Assert::assertInstanceOf(Some::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertSomeEquals')) {
    /**
     * @param mixed $actual
     * @param mixed $expected
     */
    function assertSomeEquals($expected, $actual): void
    {
        Assert::assertInstanceOf(Some::class, $actual);
        Assert::assertEquals($expected, $actual->get());
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertSuccess')) {
    /** @param mixed $actual */
    function assertSuccess($actual): void
    {
        Assert::assertInstanceOf(Success::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertNotSuccess')) {
    /** @param mixed $actual */
    function assertNotSuccess($actual): void
    {
        Assert::assertNotInstanceOf(Success::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertNotFailure')) {
    /** @param mixed $actual */
    function assertNotFailure($actual): void
    {
        Assert::assertNotInstanceOf(Failure::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertJustSuccess')) {
    /** @param mixed $actual */
    function assertJustSuccess($actual): void
    {
        Assert::assertInstanceOf(JustSuccess::class, $actual);
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertFailureReasonString')) {
    /**
     * @param mixed $actual
     */
    function assertFailureReasonString(string $expected, $actual): void
    {
        Assert::assertInstanceOf(Failure::class, $actual);
        Assert::assertEquals($expected, $actual->reason()->toString());
    }
}

if (!function_exists('j45l\maybe\Optional\Phpunit\assertFailureReasonThrowable')) {
    /**
     * @param class-string $expected
     * @param mixed $actual
     */
    function assertFailureReasonThrowable(string $expected, $actual): void
    {
        Assert::assertInstanceOf(Failure::class, $actual);
        Assert::assertInstanceOf(ThrowableReason::class, $actual->reason());
        /** @noinspection UnnecessaryAssertionInspection */
        Assert::assertInstanceOf($expected, $actual->reason()->throwable());
    }
}
