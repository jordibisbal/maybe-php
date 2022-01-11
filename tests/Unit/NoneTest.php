<?php

namespace j45l\either\Test\Unit;

use Closure;
use j45l\either\None;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\either\None */
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
}
