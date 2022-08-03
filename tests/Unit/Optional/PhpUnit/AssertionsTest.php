<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional\PhpUnit;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowable;
use function j45l\maybe\Optional\PhpUnit\assertJustSuccess;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertNotSuccess;
use function j45l\maybe\Optional\PhpUnit\assertSome;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;

/**
 * @covers ::j45l\maybe\Optional\PhpUnit\assertNone
 * @covers ::j45l\maybe\Optional\PhpUnit\assertSome
 * @covers ::j45l\maybe\Optional\PhpUnit\assertSomeEquals
 * @covers ::j45l\maybe\Optional\PhpUnit\assertSuccess
 * @covers ::j45l\maybe\Optional\PhpUnit\assertNotSuccess
 * @covers ::j45l\maybe\Optional\PhpUnit\assertJustSuccess
 * @covers ::j45l\maybe\Optional\PhpUnit\assertFailure
 * @covers ::j45l\maybe\Optional\PhpUnit\assertNotFailure
 * @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonString
 * @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowable
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class AssertionsTest extends TestCase
{
    public function testAssertNoneIsNone(): void
    {
        assertNone(None::create());
    }

    public function testAssertNoneIsNotNone(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNone(Some::from(42));
    }

    public function testAssertSomeIsSome(): void
    {
        assertSome(Some::from(42));
    }

    public function testAssertSomeEquals(): void
    {
        assertSomeEquals(42, Some::from(42));
    }

    public function testAssertSomeEqualsNot(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSomeEquals(1, Some::from(42));
    }

    public function testAssertSomeIsNotSome(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSome(None::create());
    }

    public function testAssertJustSuccessIsSuccess(): void
    {
        assertSuccess(JustSuccess::create());
    }

    public function testAssertSomeIsSuccess(): void
    {
        assertSuccess(Some::from(42));
    }

    public function testNotSuccessIsNotSuccess(): void
    {
        assertNotSuccess(None::create());
    }

    public function testNotSuccessIsNotNotSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNotSuccess(JustSuccess::create());
    }

    public function testNotFailureIsNotFailure(): void
    {
        assertNotFailure(None::create());
    }

    public function testNotFailureIsNotNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNotFailure(Failure::create());
    }

    public function testAssertJustSuccessIsJustSuccess(): void
    {
        assertJustSuccess(JustSuccess::create());
    }

    public function testAssertSomeIsNotJustSome(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertJustSuccess(Some::from(42));
    }

    public function testAssertJustSuccessIsNotJustSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertJustSuccess(None::create());
    }

    public function testAssertSuccessIsSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSuccess(None::create());
    }

    public function testAssertFailureIsFailure(): void
    {
        assertFailure(Failure::create());
    }

    public function testAssertFailureIsNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailure(None::create());
    }

    public function testAssertFailureReasonStringIs(): void
    {
        assertFailureReasonString('Failure reason', Failure::because(FailureReason::fromString('Failure reason')));
    }

    public function testAssertFailureReasonStringIsNo(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailureReasonString('Another reason', Failure::create());
    }

    public function testAssertFailureReasonStringIsNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailureReasonString('', None::create());
    }

    public function testAssertFailureReasonThrowableIsThrowable(): void
    {
        $failure = Failure::because(ThrowableReason::fromThrowable(new RuntimeException('Exception')));

        assertFailureReasonThrowable(RuntimeException::class, $failure);
        assertFailureReasonString('Exception', $failure);
    }

    public function testAssertFailureReasonThrowableIsNotThrowable(): void
    {
        $this->expectException(AssertionFailedError::class);

        assertFailureReasonThrowable(
            RuntimeException::class,
            Failure::because(FailureReason::fromString('Reason'))
        );
    }
}
