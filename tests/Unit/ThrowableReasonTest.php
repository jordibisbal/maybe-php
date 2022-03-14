<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\ThrowableReason;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringStartsWith;

/** @covers \j45l\either\ThrowableReason */
final class ThrowableReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $throwableReason = ThrowableReason::from('reason');

        self::assertInstanceOf(ThrowableReason::class, $throwableReason);
        assertStringStartsWith('reason', $throwableReason->toString());
    }

    public function testCanBeCreatedFromAThrowable(): void
    {
        $throwable = new RuntimeException('runtime reason');
        $throwableReason = ThrowableReason::fromThrowable($throwable);

        assertEquals('runtime reason', $throwableReason->toString());
        self::assertSame($throwable, $throwableReason->throwable());
    }
}
