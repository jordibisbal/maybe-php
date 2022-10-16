<?php

namespace j45l\maybe\Test\Unit\Optional\Functions;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\NoReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\reason;

/** @covers ::\j45l\maybe\Optional\reason */
class ReasonFunctionTest extends TestCase
{
    public function testGetsAReasonFromAFailure(): void
    {
        self::assertNotInstanceOf(NoReason::class, reason(Failure::create()));
    }

    public function testGetsANonReasonFromNonFailures(): void
    {
        self::assertInstanceOf(NoReason::class, reason(JustSuccess::create()));
        self::assertInstanceOf(NoReason::class, reason(Some::from(42)));
        self::assertInstanceOf(NoReason::class, reason(None::create()));
    }
}
