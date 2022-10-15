<?php

namespace j45l\maybe\Test\Unit\Optional;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\lift;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowableOf;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/** @covers ::j45l\maybe\Optional\lift */
class LiftTest extends TestCase
{
    public function testLiftingASuccessfulFunction(): void
    {
        $lifted = lift(function (): int {
            return 42;
        });
        $maybe = $lifted();

        assertSomeEquals(42, $maybe);
    }

    public function testInvokingALiftFunctionPassesParameters(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(40, 2);

        assertSomeEquals(42, $maybe);
    }

    public function testInvokingALiftFunctionLowerSomeParametersBeforeInvoking(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(40, Some::from(2));

        assertSomeEquals(42, $maybe);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingAFailingLiftFunctionReturnsAFailure(): void
    {
        $lifted = lift(function (int $first, int $second): int {
            throw new RuntimeException('An exception');
        });
        $maybe = $lifted(40, 2);

        assertFailureReasonThrowableOf(RuntimeException::class, $maybe);
        assertFailureReasonString('An exception', $maybe);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingALiftWithANoneResultsInANone(): void
    {
        $lifted = lift($this->addFunction());
        $maybe = $lifted(42, None::create());

        assertNone($maybe);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function testInvokingALiftWithAFailureResultsInAFailure(): void
    {
        $maybe = lift($this->addFunction())(42, Failure::create());

        assertFailure($maybe);
    }

    private function addFunction(): Closure
    {
        return static function (int $first, int $second): int {
            return $first + $second;
        };
    }
}
