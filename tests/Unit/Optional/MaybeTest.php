<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\lift;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class MaybeTest extends TestCase
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

        assertSomeEquals(42, $some);
    }

    public function testLiftedLiftParameters(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(41, Some::from(1));

        assertSomeEquals(42, $some);
    }

    public function testLiftedReturnsNoneWhenSomeParametersAreNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), None::create());

        assertNone($some);
        assertNotFailure($some);
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailure(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        assertFailure(lift($function)(Some::from(41), Failure::create()));
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailureNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        assertFailure(lift($function)(None::create(), Failure::create()));
    }

    public function testTakeOrElseReturnsDefault(): void
    {
        self::assertEquals(42, None::create()->takeOrElse('value', 42));
    }
}
