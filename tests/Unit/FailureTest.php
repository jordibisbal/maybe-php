<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\Failure;
use j45l\either\Parameters;
use j45l\either\Reason;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\either\Failure
 */
final class FailureTest extends TestCase
{
    public function testCanBeCreatedFromFailure(): void
    {
        $failure = Failure::from(Reason::from('reason'));

        self::assertEquals('reason', $failure->reason()->asString());
    }

    public function testAFailureMaintainsContext(): void
    {
        $succeeding = static function (): int {
            return 42;
        };

        $failing = static function (): void {
            throw new RuntimeException();
        };

        $failure = Either::start()->next($succeeding)->with(1, 2, 3)->then($failing)->resolve();

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals(Parameters::create(1, 2, 3), $failure->context()->parameters());
    }
}
