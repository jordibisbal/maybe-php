<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\Reason;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Either\Failure */
final class FailureTest extends TestCase
{
    public function testCanBeCreatedFromFailure(): void
    {
        $failure = Failure::because(Reason::fromString('reason'));

        self::assertEquals('reason', $failure->reason()->toString());
    }

    public function testFailureIsRecoverableInSink(): void
    {
        $failure = Failure::because(Reason::fromString('reason'));
        $recover = function () {
            return Some::from(42);
        };
        $recovered = $failure->orElse($recover);

        self::assertInstanceOf(Some::class, $recovered);
        self::assertEquals(42, $recovered->get());
    }

    public function testCanBeCreatedFromFailureWithOutReason(): void
    {
        $failure = Failure::create();

        self::assertEquals('Unspecified reason', $failure->reason()->toString());
    }

    public function testPipeFromNoneReturnsItself(): void
    {
        $failure = Failure::create();
        self::assertSame($failure, $failure->andThen($this->identity()));
    }

    private function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }
}
