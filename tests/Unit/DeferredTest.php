<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
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
        $some =
            Maybe::begin()->next(
                static function () {
                    return null;
                }
            )
            ->resolve()
        ;

        self::assertInstanceOf(Some::class, $some);
        self::assertNull($some->get());
    }

    public function testResolvingIsImmutableOnResolve(): void
    {
        $deferred = Some::from(42)->tag('tag A');
        $some = $deferred->resolve();

        self::assertCount(0, $deferred->context()->tagged()->someValues());
        self::assertCount(1, $some->context()->tagged()->someValues());
    }

    public function testThenCausesEvaluation(): void
    {
        $maybe = Maybe::begin()->next($this->identity(), 123)
            ->andThen($this->identity(), 456)
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
        $failure = Maybe::begin()->next($this->throwsRuntime())->resolve();
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

    public function testTakeOrElse(): void
    {
        $get42 = function () {
            return ['answer' => 42];
        };

        self::assertEquals(Some::from(42), Deferred::create($get42)->takeOrElse('answer', null));
    }

    public function testTakeOrElseNotFound(): void
    {
        $get42 = function () {
            return ['question' => 42];
        };

        self::assertEquals(
            Some::from('whoKnows'),
            Deferred::create($get42)->takeOrElse('answer', 'whoKnows')
        );
    }
}
