<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\LeftRight\lift;

/**
 * @covers \j45l\maybe\LeftRight\LeftRight
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class LeftRightMaybeTest extends TestCase
{
    public function testGetOrElse(): void
    {
        self::assertEquals(42, None::create()->getOrElse(42));
    }

    public function testLiftedReturnsSomeWhenAllParametersAreSome(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), Some::from(1));

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testLiftedLiftParameters(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(41, Some::from(1));

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testLiftedReturnsNoneWhenSomeParametersAreNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), None::create());

        self::assertInstanceOf(None::class, $some);
        self::assertNotInstanceOf(Failure::class, $some);
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailure(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), Failure::create());

        self::assertInstanceOf(Failure::class, $some);
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailureNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(None::create(), Failure::create());

        self::assertInstanceOf(Failure::class, $some);
    }

    public function testTakeOrElseReturnsDefault(): void
    {
        self::assertEquals(42, None::create()->takeOrElse('value', 42));
    }
}
