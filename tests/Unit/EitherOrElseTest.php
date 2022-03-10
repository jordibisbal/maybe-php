<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\Failure;
use j45l\either\None;
use j45l\either\Some;
use j45l\either\Success;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\None
 * @covers \j45l\either\Failure
 * @covers \j45l\either\Deferred
 * @covers \j45l\either\Success
 */
final class EitherOrElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $either = Some::from(42)->orElse(0);

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testSucceedReturnsItself(): void
    {
        $either = Success::create()->orElse(0);

        self::assertInstanceOf(Success::class, $either);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $either = None::create()->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): Either {
            return Some::from(42);
        };

        $either = Either::start()->next($fortyTwo)->OrElse(None::create());

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (Some $some): Either {
            return Some::from($some->get() + 1);
        };

        $either = Some::from(41)->map($addOne);

        self::assertInstanceOf(Either::class, $either);

        $either = $either->resolve();

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): Either {
            return Some::from($some->get() + 1);
        };

        $either = None::create()->map($addOne);

        self::assertInstanceOf(Either::class, $either);

        $either = $either->resolve();

        self::assertInstanceOf(None::class, $either);
        self::assertNotInstanceOf(Failure::class, $either);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (Either $either): Either {
            throw new RuntimeException('Should not be resolved');
        };

        $either = None::create()->map($addOne);

        self::assertInstanceOf(Either::class, $either);

        $either = $either->resolve();

        self::assertInstanceOf(None::class, $either);
        self::assertNotInstanceOf(Failure::class, $either);
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): Either {
            return None::create();
        };

        $either = Either::start()->next($none)->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }
}
