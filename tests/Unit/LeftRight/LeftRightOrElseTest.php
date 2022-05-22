<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\LeftRight\LeftRight
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class LeftRightOrElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $maybe = Some::from(42)->orElse(0);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testSucceedReturnsItself(): void
    {
        $maybe = JustSuccess::create()->orElse(0);

        self::assertInstanceOf(JustSuccess::class, $maybe);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $maybe = None::create()->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): LeftRight {
            return Some::from(42);
        };

        $maybe = Either::do($fortyTwo)->OrElse(None::create());

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (Some $some): LeftRight {
            return Some::from($some->get() + 1);
        };

        $maybe = Some::from(41)->map($addOne);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): LeftRight {
            return Some::from($some->get() + 1);
        };

        $maybe = None::create()->map($addOne);

        self::assertInstanceOf(None::class, $maybe);
        self::assertNotInstanceOf(Failure::class, $maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (LeftRight $maybe): LeftRight {
            throw new RuntimeException('Should not be resolved');
        };

        $maybe = None::create()->map($addOne);

        self::assertInstanceOf(None::class, $maybe);
        self::assertNotInstanceOf(\j45l\maybe\Either\Failure::class, $maybe);
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): LeftRight {
            return None::create();
        };

        $maybe = Either::do($none)->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }
}
