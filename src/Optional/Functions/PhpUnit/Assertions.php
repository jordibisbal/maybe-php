<?php

declare(strict_types=1);

namespace j45l\maybe\Optional\PhpUnit;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\Assert;

use function j45l\maybe\Optional\reason;

/** @param mixed $actual */
function assertNone(mixed $actual): void
{
    Assert::assertInstanceOf(None::class, $actual);
}

/** @param mixed $actual */
function assertFailure(mixed $actual): void
{
    Assert::assertInstanceOf(Failure::class, $actual);
}

/** @param mixed $actual */
function assertSome(mixed $actual): void
{
    Assert::assertInstanceOf(Some::class, $actual);
}

/**
 * @param mixed $actual
 * @param mixed $expected
 */
function assertSomeEquals(mixed $expected, mixed $actual): void
{
    Assert::assertInstanceOf(Some::class, $actual);
    Assert::assertEquals($expected, $actual->get());
}

/** @param mixed $actual */
function assertSuccess(mixed $actual): void
{
    Assert::assertInstanceOf(Success::class, $actual);
}

/** @param mixed $actual */
function assertNotSuccess(mixed $actual): void
{
    Assert::assertNotInstanceOf(Success::class, $actual);
}

/** @param mixed $actual */
function assertNotFailure(mixed $actual): void
{
    Assert::assertNotInstanceOf(Failure::class, $actual);
}
/** @param mixed $actual */
function assertJustSuccess(mixed $actual): void
{
    Assert::assertInstanceOf(JustSuccess::class, $actual);
}

/**
 * @param mixed $actual
 */
function assertFailureReasonString(string $expected, mixed $actual): void
{
    Assert::assertInstanceOf(Failure::class, $actual);
    Assert::assertEquals($expected, $actual->reason()->toString());
}

/**
 * @param class-string $expected
 * @param mixed $actual
 */
function assertFailureReasonThrowableOf(string $expected, mixed $actual): void
{
    assertReasonIsAThrowableOf($expected, reason($actual));
}

/** @param class-string $expected */
function assertReasonIsAThrowableOf(string $expected, Reason $reason): void
{
    Assert::assertInstanceOf(ThrowableReason::class, $reason);
    /** @noinspection UnnecessaryAssertionInspection */
    Assert::assertInstanceOf($expected, $reason->throwable());
}
