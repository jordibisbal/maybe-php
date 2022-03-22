<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Result\Success;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Result\Success */
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
