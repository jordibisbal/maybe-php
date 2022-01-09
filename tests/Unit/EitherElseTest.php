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
 * @covers \j45l\either\Deferred
 * @covers \j45l\either\Succeed
 */
final class EitherElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $either = Some::from(42)->orElse(0);

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testSucceedReturnsItself(): void
    {
        $either = Succeed::create()->orElse(0);

        self::assertInstanceOf(Succeed::class, $either);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $either = None::create()->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): Either {
            return Some::from(42);
        };

        $either = Either::do($fortyTwo)->OrElse(None::create());

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): Either {
            return None::create();
        };

        $either = Either::do($none)->OrElse(Some::from(42));

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(42, $either->value());
    }
}
