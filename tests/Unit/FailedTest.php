<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\Failed;
use j45l\either\Parameters;
use j45l\either\Reason;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FailedTest extends TestCase
{
    public function testCanBeCreatedFromFailure(): void
    {
        $failed = Failed::from(Reason::from('reason'));

        self::assertEquals('reason', $failed->reason()->asString());
    }

    public function testAFailureMaintainsContext(): void
    {
        $succeeding = static function (): int {
            return 42;
        };

        $failing = static function (): void {
            throw new RuntimeException();
        };

        $failure = Either::do($succeeding)->with(1, 2, 3)->then($failing)->resolve();

        self::assertInstanceOf(Failed::class, $failure);
        self::assertEquals(Parameters::create(1, 2, 3), $failure->context()->parameters());
    }
}
