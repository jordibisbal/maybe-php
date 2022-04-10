<?php

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\None;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\None */
class NoneTest extends TestCase
{
    public function testPipeFromNoneReturnsItself(): void
    {
        $none = None::create();
        self::assertSame($none, $none->pipe($this->identity()));
    }

    public function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }

    public function testGetOrElse(): void
    {
        self::assertEquals(42, None::create()->getOrElse(42));
    }
}
