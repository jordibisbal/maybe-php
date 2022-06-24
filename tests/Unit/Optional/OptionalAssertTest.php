<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Either\Failure
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalAssertTest extends TestCase
{
    public function testAssertingNoneReturnsFailure(): void
    {
        $maybe = None::create()->assert(false);

        assertFailureReasonString('failed assertion', $maybe);
    }

    public function testAssertingNoneReturnsFailureWithMessage(): void
    {
        $maybe = None::create()->assert(false, 'Failed!');

        assertFailureReasonString('Failed!', $maybe);
    }

    public function testAssertingFailureReturnsSame(): void
    {
        $failure = Failure::create();
        $result = $failure->assert(false);

        self::assertSame($failure, $result);
    }

    public function testAssertingFalseReturnsFailure(): void
    {
        $maybe = Some::from(1)
            ->assert(false);

        assertFailureReasonString('failed assertion', $maybe);
    }

    public function testAssertingFalseReturnsFailureWithMessage(): void
    {
        $maybe = Some::from(1)
            ->assert(false, 'Failed!');

        assertFailureReasonString('Failed!', $maybe);
    }

    public function testAssertingFailingClosureReturnsFailure(): void
    {
        $maybe = Some::from(1)
            ->assert(
                function () {
                    throw new RuntimeException();
                }
            );

        assertFailureReasonString('failed assertion', $maybe);
    }

    public function testAssertingTrueReturnsOriginalOptionalIfValued(): void
    {
        $maybe = Some::from(42);
        $result = $maybe->assert(true);

        self::assertSame($maybe, $result);
    }

    public function testAssertingCallbackTrueReturnsOriginalOptionalIfValued(): void
    {
        $maybe = Some::from(42);
        $result = $maybe->assert(function (Some $some): bool {
            return $some->get() === 42;
        });

        self::assertSame($maybe, $result);
    }

    public function testAssertingCallbackFalseReturnsFailure(): void
    {
        $maybe = Some::from(42)->assert(function (Some $some): bool {
            return $some->get() !== 42;
        });

        assertFailureReasonString('failed assertion', $maybe);
    }

    public function testAssertingTrueReturnsFailureIfNotValued(): void
    {
        $maybe = JustSuccess::create()->assert(true);

        assertFailureReasonString('failed assertion', $maybe);
    }
}
