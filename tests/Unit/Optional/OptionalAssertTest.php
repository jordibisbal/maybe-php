<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalAssertTest extends TestCase
{
    public function testAssertingFailureReturnsFailure(): void
    {
        $maybe = Failure::create()->assert(false);

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertEquals('failed assertion', $maybe->reason()->toString());
    }

    public function testAssertingFailureReturnsFailureWithMessage(): void
    {
        $maybe = Failure::create()->assert(false, 'Failed!');

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertEquals('Failed!', $maybe->reason()->toString());
    }

    public function testAssertingFalseReturnsFailure(): void
    {
        $maybe = Some::from(1)
            ->assert(false);

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertEquals('failed assertion', $maybe->reason()->toString());
    }

    public function testAssertingFalseReturnsFailureWithMessage(): void
    {
        $maybe = Some::from(1)
            ->assert(false, 'Failed!');

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertEquals('Failed!', $maybe->reason()->toString());
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
        self::assertEquals('failed assertion', $maybe->reason()->toString());
    }

    public function testAssertingTrueReturnsFailureIfNotValued(): void
    {
        $maybe = JustSuccess::create()->assert(true);

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertEquals('failed assertion', $maybe->reason()->toString());
    }
}
