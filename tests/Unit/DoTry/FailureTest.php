<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\DoTry;

use Closure;
use j45l\maybe\Context\Parameters;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Reason;
use j45l\maybe\Maybe;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\DoTry\Failure
 */
final class FailureTest extends TestCase
{
    public function testCanBeCreatedFromFailure(): void
    {
        $failure = Failure::from(Reason::fromString('reason'));

        self::assertEquals('reason', $failure->reason()->toString());
    }

    public function testFailureIsRecoverablInSink(): void
    {
        $failure = Failure::from(Reason::fromString('reason'));
        $recover = function () {
            return Some::from(42);
        };
        $recovered = $failure->sink($recover);

        self::assertInstanceOf(Some::class, $recovered);
        self::assertEquals(42, $recovered->get());
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

    public function testPipeFromNoneReturnsItself(): void
    {
        $failure = Failure::create();
        self::assertSame($failure, $failure->pipe($this->identity()));
    }

    private function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }
}
