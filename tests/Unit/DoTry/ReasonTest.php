<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\DoTry;

use j45l\maybe\DoTry\Reason;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/** @covers \j45l\maybe\DoTry\Reason */
final class ReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $throwableFailure = Reason::fromString('reason');

        assertEquals('reason', $throwableFailure->toString());
    }
}
