<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\ThrowableReason;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringStartsWith;

final class ThrowableReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $throwableReason = ThrowableReason::from('reason');

        self::assertInstanceOf(ThrowableReason::class, $throwableReason);
        assertStringStartsWith('reason', $throwableReason->asString());
    }

    public function testCanBeCreatedFromAThrowable(): void
    {
        $throwable = new RuntimeException('runtime reason');
        $throwableReason = ThrowableReason::fromThrowable($throwable);

        assertEquals('runtime reason', $throwableReason->asString());
        self::assertSame($throwable, $throwableReason->throwable());
    }
}
