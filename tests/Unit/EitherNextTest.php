<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\None;
use j45l\either\Some;
use j45l\either\Succeed;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\None
 * @covers \j45l\either\Failed
 * @covers \j45l\either\Succeed
 */
final class EitherNextTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $either = Some::from(0)
            ->next(function (): Either {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testSucceedReturnsLastValue(): void
    {
        $either = Succeed::create()
            ->next(function (): Either {
                return Some::from(42);
            })->resolve()
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testNoneReturnsNextValue(): void
    {
        $either = None::create()->next(Some::from(42));

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testDeferredSomeReturnsNextValue(): void
    {
        $either =
            Either::do(static function (): Either {
                return Some::from(42);
            })
            ->next(None::create())
        ;

        self::assertInstanceOf(None::class, $either);
    }

    public function testDeferredNoneReturnsNoneValue(): void
    {
        $either =
            Either::do(static function (): Either {
                return None::create();
            })
            ->next(Some::from(42))
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }
}
