<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\Deferred
 */
final class DeferredTest extends TestCase
{
    public function testRetainsParametersAfterEvaluation(): void
    {
        $maybe =
            Deferred::create($this->identity())
            ->with(123)
            ->resolve()
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals([123], $maybe->context()->parameters()->asArray());
    }

    public function testCanOverrideAndRetainsParametersAfterEvaluation(): void
    {
        $maybe =
            Deferred::create($this->identity())
                ->with(123)
                ->resolve(42, 43)
        ;

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals([42, 43], $maybe->context()->parameters()->asArray());
    }

    public function testEvaluationWithNullResultReturnsANone(): void
    {
        $none =
            Maybe::start()->next(
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
        $maybe = Maybe::start()->next($this->identity())->with(123)
            ->andThen($this->identity())->with(456)
        ;

        $trail = $maybe->trail()->asArray();

        self::assertInstanceOf(Some::class, $trail[0]);
        self::assertInstanceOf(Some::class, $trail[1]);
    }

    public function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }

    public function testEvaluatingAThrowingClosureResultsInAFailure(): void
    {
        $failure = Maybe::start()->next($this->throwsRuntime())->resolve();
        self::assertInstanceOf(Failure::class, $failure);

        self::assertEquals('Runtime !', $failure->reason()->toString());
    }

    public function throwsRuntime(): Closure
    {
        return static function (): void {
            throw new RuntimeException('Runtime !');
        };
    }

    public function testGetOrElse(): void
    {
        $get42 = function () {
            return 42;
        };

        self::assertEquals(42, Deferred::create($get42)->getOrElse(null));
    }
}
