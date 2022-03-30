<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\None
 * @covers \j45l\maybe\DoTry\Failure
 * @covers \j45l\maybe\Deferred
 * @covers \j45l\maybe\DoTry\Success
 */
final class MaybeOrElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $maybe = Some::from(42)->orElse(0);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testSucceedReturnsItself(): void
    {
        $maybe = Success::create()->orElse(0);

        self::assertInstanceOf(Success::class, $maybe);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $maybe = None::create()->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): Maybe {
            return Some::from(42);
        };

        $maybe = Maybe::start()->next($fortyTwo)->OrElse(None::create());

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (Some $some): Maybe {
            return Some::from($some->get() + 1);
        };

        $maybe = Some::from(41)->map($addOne);

        self::assertInstanceOf(Maybe::class, $maybe);

        $maybe = $maybe->resolve();

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): Maybe {
            return Some::from($some->get() + 1);
        };

        $maybe = None::create()->map($addOne);

        self::assertInstanceOf(Maybe::class, $maybe);

        $maybe = $maybe->resolve();

        self::assertInstanceOf(None::class, $maybe);
        self::assertNotInstanceOf(Failure::class, $maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (Maybe $maybe): Maybe {
            throw new RuntimeException('Should not be resolved');
        };

        $maybe = None::create()->map($addOne);

        self::assertInstanceOf(Maybe::class, $maybe);

        $maybe = $maybe->resolve();

        self::assertInstanceOf(None::class, $maybe);
        self::assertNotInstanceOf(Failure::class, $maybe);
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): Maybe {
            return None::create();
        };

        $maybe = Maybe::start()->next($none)->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }
}
