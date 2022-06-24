<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either;

use j45l\maybe\Either\Reason;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/** @covers \j45l\maybe\Either\Reason */
final class ReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $reason = Reason::fromString('reason');

        assertEquals('reason', $reason->toString());
    }

    public function testCanBeCreatedFromAFormat(): void
    {
        $reason = Reason::fromFormatted('because %s and %s', 'one', 'another');

        assertEquals('because one and another', $reason->toString());
    }
}
