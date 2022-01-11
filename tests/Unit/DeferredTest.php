<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\Failure;
use j45l\either\None;
use j45l\either\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\Deferred
 */
final class DeferredTest extends TestCase
{
    public function testRetainsParametersAfterEvaluation(): void
    {
        $either =
            Either::do($this->identity())
            ->with(123)
            ->resolve()
        ;

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals([123], $either->context()->parameters()->asArray());
    }

    public function testEvaluationWithNullResultReturnsANone(): void
    {
        $none =
            Either::do(
                static function () {
                    return null;
                }
            )
                ->resolve()
        ;

        self::assertInstanceOf(None::class, $none);
    }

    public function testThenCausesEvaluation(): void
    {
        $either = Either::do($this->identity())->with(123)
            ->then($this->identity())->with(456)
        ;

        $trail = $either->trail()->asArray();

        self::assertInstanceOf(Some::class, $trail[0]);
        self::assertInstanceOf(Some::class, $trail[1]);
    }

    /**
     * @return \Closure
     */
    public function identity(): \Closure
    {
        return static function ($value) {
            return $value;
        };
    }

    public function testEvaluatingAThrowingClosureResultsInAFailure(): void
    {
        $failure = Either::do($this->throwsRuntime())->resolve();
        self::assertInstanceOf(Failure::class, $failure);

        self::assertEquals('Runtime !', $failure->reason()->asString());
    }

    public function throwsRuntime(): \Closure
    {
        return static function (): void {
            throw new RuntimeException('Runtime !');
        };
    }
}
