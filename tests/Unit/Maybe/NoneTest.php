<?php

namespace j45l\maybe\Test\Unit\Maybe;

use j45l\maybe\Maybe\None;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Maybe\None */
class NoneTest extends TestCase
{
    public function testGetOrElse(): void
    {
        self::assertEquals(42, None::create()->getOrElse(42));
    }
}
