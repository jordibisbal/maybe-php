<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertJustSuccess;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalOrElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $maybe = Some::from(42)->orElse(0);

        assertSomeEquals(42, $maybe);
    }

    public function testSucceedReturnsItself(): void
    {
        $maybe = JustSuccess::create()->orElse(0);

        assertJustSuccess($maybe);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $maybe = None::create()->OrElse(Some::from(42));

        assertSomeEquals(42, $maybe);
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): Optional {
            return Some::from(42);
        };

        $maybe = Either::do($fortyTwo)->OrElse(None::create());

        assertSomeEquals(42, $maybe);
    }

    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (int $x): int {
            return $x + 1;
        };

        $maybe = Some::from(41)->map($addOne);

        assertSomeEquals(42, $maybe);
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): Optional {
            return Some::from($some->get() + 1);
        };

        $maybe = None::create()->map($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (Optional $maybe): Optional {
            throw new RuntimeException('Should not be resolved');
        };

        $maybe = None::create()->map($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): Optional {
            return None::create();
        };

        $maybe = Either::do($none)->OrElse(Some::from(42));

        assertSomeEquals(42, $maybe);
    }
}
