<?php

namespace j45l\maybe\Test\Unit;

use j45l\maybe\None;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\None */
class NoneTest extends TestCase
{
    public function testGetOrElse(): void
    {
        self::assertEquals(42, None::create()->getOrElse(42));
    }
}
