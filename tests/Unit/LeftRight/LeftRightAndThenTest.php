<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use j45l\maybe\Either\JustSuccess;
use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Functions\safe;

/**
 * @covers \j45l\maybe\LeftRight\LeftRight
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class LeftRightAndThenTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $maybe = Some::from(0)
            ->andThen(function (): LeftRight {
                return Some::from(42);
            })
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testSucceedReturnsLastValue(): void
    {
        $maybe = JustSuccess::create()
            ->andThen(function (): LeftRight {
                return Some::from(42);
            })
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
            safe(static function (): LeftRight {
                return Some::from(42);
            })()
            ->andThen(None::create())
        ;

        self::assertInstanceOf(None::class, $maybe);
    }
}
