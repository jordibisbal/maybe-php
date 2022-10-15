<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either\Reasons;

use j45l\maybe\Either\Reasons\NoReason;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertStringStartsWith;

/** @covers \j45l\maybe\Either\Reasons\NoReason */
final class NoReasonTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $throwableReason = NoReason::create();

        assertStringStartsWith('No reason.', $throwableReason->toString());
    }
}
