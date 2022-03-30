<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Success;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\None
 * @covers \j45l\maybe\DoTry\Failure
 * @covers \j45l\maybe\DoTry\Success
 */
final class MaybeThenTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $maybe = Some::from(0)
            ->andThen(function (): Maybe {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testSucceedReturnsLastValue(): void
    {
        $maybe = Success::create()
            ->andThen(function (): Maybe {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testNoneReturnsNoneValue(): void
    {
        $maybe = None::create()->andThen(Some::from(42));

        self::assertInstanceOf(None::class, $maybe);
    }

    public function testDeferredSomeReturnsNextValue(): void
    {
        $maybe =
            Maybe::start()->next(static function (): Maybe {
                return Some::from(42);
            })
            ->andThen(None::create())
        ;

        self::assertInstanceOf(None::class, $maybe);
    }

    public function testDeferredNoneReturnsNoneValue(): void
    {
        $maybe =
            Maybe::start()->next(static function (): Maybe {
                return None::create();
            })
            ->andThen(Some::from(42))
        ;

        self::assertInstanceOf(None::class, $maybe);
    }
}
