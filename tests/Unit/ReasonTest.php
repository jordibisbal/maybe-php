<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Reason;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

final class ReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $throwableFailure = Reason::from('reason');

        assertEquals('reason', $throwableFailure->asString());
    }
}
