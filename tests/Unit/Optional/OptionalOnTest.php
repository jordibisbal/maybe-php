<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalOnTest extends TestCase
{
    public function testMatchingClassReturnsValue(): void
    {
        $maybe = Some::from(1)
            ->on(
                Some::class,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMatchingClassCurrentIsPassed(): void
    {
        $maybe = Some::from(41)
            ->on(
                Some::class,
                function (Some $some) {
                    return Some::from($some->get() + 1);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testNotMatchingClassBypasses(): void
    {
        $maybe = Some::from(1)
            ->on(
                None::class,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }
}
