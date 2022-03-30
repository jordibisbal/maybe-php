<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\Some;
use j45l\maybe\Tags\TagCreator;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\lift;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\None
 * @covers \j45l\maybe\Some
 * @covers \j45l\maybe\DoTry\Failure
 * @covers \j45l\maybe\Deferred
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class MaybeTest extends TestCase
{
    public function testAnMaybeHaveAContextWithParameters(): void
    {
        $maybe = None::create();
        $maybeWitContext = $maybe->with(1, 2, 3);

        self::assertEquals([], $maybe->context()->parameters()->asArray());
        self::assertEquals([1, 2, 3], $maybeWitContext->context()->parameters()->asArray());
    }

    public function testAfterChangingContextTrailIsNotLost(): void
    {
        $maybe = Some::from(1)->andThen(2)->andThen(3);
        $trail = $maybe->trail();

        $maybe = $maybe->with(None::create());

        self::assertEquals($trail->butLast(), $maybe->trail()->butLast());
    }

    public function testNextWithMaybeTrailIsNotLost(): void
    {
        $maybe = Some::from(1)->andThen(2)->andThen(3);
        $trail = $maybe->trail();

        $maybe = $maybe->next(None::create());

        self::assertEquals($trail->butLast(), $maybe->trail()->butLast()->butLast());
    }

    public function testTrailWithNewTagIsSet(): void
    {
        $maybe = Maybe::start()->withTag('tag');

        $this->assertEquals(TagCreator::from('tag'), $maybe->context()->tag());
    }

    public function testTrailWithTagNextIsSet(): void
    {
        $maybe = Maybe::start()->tagNext('tag', 42);

        $this->assertEquals(TagCreator::from('tag'), $maybe->context()->tag());
        $this->assertInstanceOf(Some::class, $maybe);
        $this->assertEquals(42, $maybe->get());
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
