<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Result\Success;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\either\Result\Success */
final class SuccessTest extends TestCase
{
    public function testHasTrueValue(): void
    {
        $some = Success::create();

        self::assertIsBool($some->get());
        self::assertTrue($some->get());
    }

    public function testIgnoresValueWhenCreateUsingFrom(): void
    {
        $some = Success::from(42);

        self::assertIsBool($some->get());
        self::assertTrue($some->get());
    }
}
