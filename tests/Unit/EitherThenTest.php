<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\None;
use j45l\either\Some;
use j45l\either\Success;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\None
 * @covers \j45l\either\Failure
 * @covers \j45l\either\Success
 */
final class EitherThenTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $either = Some::from(0)
            ->then(function (): Either {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testSucceedReturnsLastValue(): void
    {
        $either = Success::create()
            ->then(function (): Either {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->get());
    }

    public function testNoneReturnsNoneValue(): void
    {
        $either = None::create()->then(Some::from(42));

        self::assertInstanceOf(None::class, $either);
    }

    public function testDeferredSomeReturnsNextValue(): void
    {
        $either =
            Either::start()->next(static function (): Either {
                return Some::from(42);
            })
            ->then(None::create())
        ;

        self::assertInstanceOf(None::class, $either);
    }

    public function testDeferredNoneReturnsNoneValue(): void
    {
        $either =
            Either::start()->next(static function (): Either {
                return None::create();
            })
            ->then(Some::from(42))
        ;

        self::assertInstanceOf(None::class, $either);
    }
}
