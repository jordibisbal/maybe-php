<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\DoTry;

use j45l\maybe\DoTry\Success;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\DoTry\Success */
final class SuccessTest extends TestCase
{
    public function testHasTrueValue(): void
    {
        $some = Success::create();

        self::assertIsBool($some->get());
        self::assertTrue($some->get());
    }

    public function testHasValueWhenCreateUsingFrom(): void
    {
        $some = Success::from(42);

        self::assertInstanceOf(Success::class, $some);
        self::assertEquals(42, $some->get());
    }
}
