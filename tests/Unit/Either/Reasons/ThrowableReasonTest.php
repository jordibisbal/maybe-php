<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either\Reasons;

use j45l\maybe\Either\Reasons\ThrowableReason;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringStartsWith;

/** @covers \j45l\maybe\Either\Reasons\ThrowableReason */
final class ThrowableReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $throwableReason = ThrowableReason::fromString('reason');

        assertStringStartsWith('reason', $throwableReason->toString());
    }

    public function testCanBeCreatedFromAFormat(): void
    {
        $reason = ThrowableReason::fromFormatted('because %s and %s', 'one', 'another');

        assertEquals('because one and another', $reason->toString());
    }

    public function testCanBeCreatedFromAThrowable(): void
    {
        $throwable = new RuntimeException('runtime reason');
        $throwableReason = ThrowableReason::fromThrowable($throwable);

        assertEquals('runtime reason', $throwableReason->toString());
        self::assertSame($throwable, $throwableReason->throwable());
    }
}
