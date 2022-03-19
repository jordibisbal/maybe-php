<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\None;
use j45l\either\Result\Failure;
use j45l\either\Some;
use j45l\either\Tags\TagCreator;
use PHPUnit\Framework\TestCase;

use function j45l\either\lift;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\None
 * @covers \j45l\either\Some
 * @covers \j45l\either\Result\Failure
 * @covers \j45l\either\Deferred
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class EitherTest extends TestCase
{
    public function testAnEitherHaveAContextWithParameters(): void
    {
        $either = None::create();
        $eitherWitContext = $either->with(1, 2, 3);

        self::assertEquals([], $either->context()->parameters()->asArray());
        self::assertEquals([1, 2, 3], $eitherWitContext->context()->parameters()->asArray());
    }

    public function testAfterChangingContextTrailIsNotLost(): void
    {
        $either = Some::from(1)->andThen(2)->andThen(3);
        $trail = $either->trail();

        $either = $either->with(None::create());

        self::assertEquals($trail->butLast(), $either->trail()->butLast());
    }

    public function testNextWithEitherTrailIsNotLost(): void
    {
        $either = Some::from(1)->andThen(2)->andThen(3);
        $trail = $either->trail();

        $either = $either->next(None::create());

        self::assertEquals($trail->butLast(), $either->trail()->butLast()->butLast());
    }

    public function testTrailWithNewTagIsSet(): void
    {
        $either = Either::start()->withTag('tag');

        $this->assertEquals(TagCreator::from('tag'), $either->context()->tag());
    }

    public function testTrailWithTagNextIsSet(): void
    {
        $either = Either::start()->tagNext('tag', 42);

        $this->assertEquals(TagCreator::from('tag'), $either->context()->tag());
        $this->assertInstanceOf(Some::class, $either);
        $this->assertEquals(42, $either->get());
    }

    public function testGetOrElse(): void
    {
        self::assertEquals(42, None::create()->getOrElse(42));
    }

    public function testLiftedReturnsSomeWhenAllParametersAreSome(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), Some::from(1));

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testLiftedLiftParameters(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(41, Some::from(1));

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testLiftedReturnsNoneWhenSomeParametersAreNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), None::create());

        self::assertInstanceOf(None::class, $some);
        self::assertNotInstanceOf(Failure::class, $some);
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailure(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(Some::from(41), Failure::create());

        self::assertInstanceOf(Failure::class, $some);
    }

    public function testLiftedReturnsFailureWhenSomeParametersAreFailureNone(): void
    {
        $function = function ($one, $another) {
            return $one + $another;
        };

        $some = lift($function)(None::create(), Failure::create());

        self::assertInstanceOf(Failure::class, $some);
    }
}
