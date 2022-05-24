<?php

namespace j45l\maybe\Test\Unit\Functions;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\LeftRight\lift;

/** @covers ::j45l\maybe\LeftRight\lift */
class LiftTest extends TestCase
{
    public function testLiftingASuccessfulFunction(): void
    {
        $lifted = lift(function (): int {
            return 42;
        });
        $maybe = $lifted();

        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
    }

    public function testInvokingALiftFunctionPassesParameters(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(40, 2);

        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
    }

    public function testInvokingALiftFunctionLowerSomeParametersBeforeInvoking(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(40, Some::from(2));

        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingAFailingLiftFunctionReturnsAFailure(): void
    {
        $lifted = lift(function (int $first, int $second): int {
            throw new RuntimeException('An exception');
        });
        $maybe = $lifted(40, 2);

        $this->assertInstanceOf(Failure::class, $maybe);
        $this->assertEquals('An exception', $maybe->reason()->toString());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingALiftWithANoneResultsInANone(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(42, None::create());

        $this->assertInstanceOf(None::class, $maybe);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingALiftWithAFailureResultsInAFailure(): void
    {
        $maybe = lift($this->addFunction())(42, Failure::create());

        $this->assertInstanceOf(Failure::class, $maybe);
    }

    private function addFunction(): Closure
    {
        return function (int $first, int $second): int {
            return $first + $second;
        };
    }
}
