<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Deferred;
use j45l\maybe\None;
use j45l\maybe\Some;
use LogicException;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\assertSome;

/** @covers ::j45l\maybe\assertSome */
final class AssertSomeTest extends TestCase
{
    public function testAssertSame(): void
    {
        $this->assertEquals(42, assertSome(Some::from(42))->get());
    }

    public function testThrowsExceptionWhenNone(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unable to get value');

        assertSome(None::create());
    }

    public function testThrowsExceptionWhenDeferred(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unable to get value');

        assertSome(Deferred::create(function () {
            return Some::from(42);
        }));
    }
}
