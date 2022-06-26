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
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function PHPUnit\Framework\assertEquals;

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

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('failed assertion', $maybe);
        assertNone($maybe->reason()->subject());
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
        assertNone($failure->reason()->subject());
    }

    public function testAssertingFalseReturnsFailure(): void
    {
        $maybe = Some::from(1)
            ->assert(false);

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('failed assertion', $maybe);
        assertEquals(1, $maybe->reason()->subject()->getOrElse(null));
    }

    public function testAssertingFalseReturnsFailureWithMessage(): void
    {
        $maybe = Some::from(1)
            ->assert(false, 'Failed!');

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('Failed!', $maybe);
        assertEquals(1, $maybe->reason()->subject()->getOrElse(null));
    }

    public function testAssertingFailingClosureReturnsFailure(): void
    {
        $maybe = Some::from(1)
            ->assert(
                function () {
                    throw new RuntimeException();
                }
            );

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('failed assertion', $maybe);
        assertEquals(1, $maybe->reason()->subject()->getOrElse(null));
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

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('failed assertion', $maybe);
        assertEquals(42, $maybe->reason()->subject()->getOrElse(null));
    }

    public function testAssertingTrueReturnsFailureIfNotValued(): void
    {
        $maybe = JustSuccess::create()->assert(true);

        self::assertInstanceOf(Failure::class, $maybe);
        assertFailureReasonString('failed assertion', $maybe);
        assertEquals(42, $maybe->reason()->subject()->getOrElse(42));
    }
}
