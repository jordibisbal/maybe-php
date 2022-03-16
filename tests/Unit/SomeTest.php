<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Some;
use j45l\either\Test\Unit\Stubs\MutableInteger;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\either\Some */
final class SomeTest extends TestCase
{
    public function testResolvesToItself(): void
    {
        $some = Some::from(42);

        self::assertSame($some, $some->resolve());
    }

    public function testCloningClonesValueWhenObject(): void
    {
        $some = Some::from(MutableInteger::fromInt(42));
        $clone = clone $some;

        $value = $some->get();
        $clonedValue = $clone->get();

        self::assertInstanceOf(MutableInteger::class, $clonedValue);
        self::assertInstanceOf(MutableInteger::class, $value);

        $clonedValue->change(0);

        self::assertNotSame($some, $clone);
        self::assertNotSame($some->get(), $clone->get());

        self::assertEquals(42, $value->asInt());
        self::assertEquals(0, $clonedValue->asInt());
    }

    public function testCloningClonesValueWhenScalar(): void
    {
        $some = Some::from(42);
        $clone = clone $some;

        self::assertEquals(42, $some->get());
        self::assertEquals(42, $clone->get());
    }

    public function testCanOverrideAndRetainsParametersAfterEvaluation(): void
    {
        $some =
            Some::from(42)
                ->with(123)
                ->resolve(124, 125)
        ;

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
        self::assertEquals([124, 125], $some->context()->parameters()->asArray());
    }

    public function testGetOrElse(): void
    {
        self::assertEquals(42, Some::from(42)->getOrElse(null));
    }
}
