<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Context\Parameters;
use j45l\maybe\Maybe;
use j45l\maybe\Result\Failure;
use j45l\maybe\Result\Reason;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Result\Failure
 */
final class FailureTest extends TestCase
{
    public function testCanBeCreatedFromFailure(): void
    {
        $failure = Failure::from(Reason::fromString('reason'));

        self::assertEquals('reason', $failure->reason()->toString());
    }

    public function testCanBeCreatedFromFailureWithOutReason(): void
    {
        $failure = Failure::create();

        self::assertEquals('Unspecified reason', $failure->reason()->toString());
    }

    public function testAFailureMaintainsContext(): void
    {
        $succeeding = static function (): int {
            return 42;
        };

        $failing = static function (): void {
            throw new RuntimeException();
        };

        $failure = Maybe::start()->next($succeeding)->with(1, 2, 3)->andThen($failing)->resolve();

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals(Parameters::create(1, 2, 3), $failure->context()->parameters());
    }
}
