<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Success;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\either\Success */
final class SuccessTest extends TestCase
{
    public function testHasTrueValue(): void
    {
        $some = Success::create();

        self::assertIsBool($some->value());
        self::assertTrue($some->value());
    }

    public function testIgnoresValueWhenCreateUsingFrom(): void
    {
        $some = Success::from(42);

        self::assertIsBool($some->value());
        self::assertTrue($some->value());
    }
}
