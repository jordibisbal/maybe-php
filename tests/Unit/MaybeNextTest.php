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
final class MaybeNextTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $maybe = Some::from(0)
            ->next(function (): Maybe {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testSuccessReturnsLastValue(): void
    {
        $maybe = Success::create()
            ->next(function (): Maybe {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testNoneReturnsNextValue(): void
    {
        $maybe = None::create()->next(Some::from(42));

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testDeferredSomeReturnsNextValue(): void
    {
        $maybe =
            Maybe::start()->next(static function (): Maybe {
                return Some::from(42);
            })
            ->next(None::create())
        ;

        self::assertInstanceOf(None::class, $maybe);
    }

    public function testDeferredNoneReturnsNoneValue(): void
    {
        $maybe =
            Maybe::start()->next(static function (): Maybe {
                return None::create();
            })
            ->next(Some::from(42))
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }
}
