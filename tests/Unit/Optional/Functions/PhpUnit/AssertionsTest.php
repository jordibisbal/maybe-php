<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional\Functions\PhpUnit;

use Exception;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function get_class as getClass;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowableOf;
use function j45l\maybe\Optional\PhpUnit\assertJustSuccess;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertNotSuccess;
use function j45l\maybe\Optional\PhpUnit\assertReasonIsAThrowableOf;
use function j45l\maybe\Optional\PhpUnit\assertSome;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class AssertionsTest extends TestCase
{
    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNone */
    public function testAssertNoneIsNone(): void
    {
        assertNone(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNone */
    public function testAssertNoneIsNotNone(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNone(Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSome */
    public function testAssertSomeIsSome(): void
    {
        assertSome(Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSomeEquals */
    public function testAssertSomeEquals(): void
    {
        assertSomeEquals(42, Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSomeEquals */
    public function testAssertSomeEqualsNot(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSomeEquals(1, Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSome */
    public function testAssertSomeIsNotSome(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSome(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSuccess */
    public function testAssertJustSuccessIsSuccess(): void
    {
        assertSuccess(JustSuccess::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSuccess */
    public function testAssertSomeIsSuccess(): void
    {
        assertSuccess(Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNotSuccess */
    public function testNotSuccessIsNotSuccess(): void
    {
        assertNotSuccess(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNotSuccess */
    public function testNotSuccessIsNotNotSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNotSuccess(JustSuccess::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNotFailure */
    public function testNotFailureIsNotFailure(): void
    {
        assertNotFailure(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertNotFailure */
    public function testNotFailureIsNotNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertNotFailure(Failure::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertJustSuccess */
    public function testAssertJustSuccessIsJustSuccess(): void
    {
        assertJustSuccess(JustSuccess::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertJustSuccess */
    public function testAssertSomeIsNotJustSome(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertJustSuccess(Some::from(42));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertJustSuccess */
    public function testAssertJustSuccessIsNotJustSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertJustSuccess(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertSuccess */
    public function testAssertSuccessIsSuccess(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertSuccess(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailure */
    public function testAssertFailureIsFailure(): void
    {
        assertFailure(Failure::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailure */
    public function testAssertFailureIsNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailure(None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonString */
    public function testAssertFailureReasonStringIs(): void
    {
        assertFailureReasonString('Failure reason', Failure::because(FailureReason::fromString('Failure reason')));
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonString */
    public function testAssertFailureReasonStringIsNo(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailureReasonString('Another reason', Failure::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonString */
    public function testAssertFailureReasonStringIsNotFailure(): void
    {
        $this->expectException(AssertionFailedError::class);
        assertFailureReasonString('', None::create());
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowableOf */
    public function testAssertFailureReasonThrowableIsThrowable(): void
    {
        $failure = Failure::because(ThrowableReason::fromThrowable(new RuntimeException('Exception')));

        assertFailureReasonThrowableOf(RuntimeException::class, $failure);
        assertFailureReasonString('Exception', $failure);
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertReasonIsAThrowableOf */
    public function testAssertFailureReasonThrowableIsNotThrowable(): void
    {
        $this->expectException(AssertionFailedError::class);

        assertFailureReasonThrowableOf(
            Exception::class,
            Failure::because(FailureReason::fromString('Reason'))
        );
    }

    /** @covers ::j45l\maybe\Optional\PhpUnit\assertReasonIsAThrowableOf */
    public function testAssertReasonIsAThrowableOf(): void
    {
        $myException = new class () extends Exception {
        };

        $reason = ThrowableReason::fromThrowable($myException);

        assertReasonIsAThrowableOf(
            getClass($myException),
            $reason
        );
    }
}
