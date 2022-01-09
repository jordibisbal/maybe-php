<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Some;
use j45l\either\Test\Unit\Stubs\MutableInteger;
use PHPUnit\Framework\TestCase;

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

        $value = $some->value();
        $clonedValue = $clone->value();

        self::assertInstanceOf(MutableInteger::class, $clonedValue);
        self::assertInstanceOf(MutableInteger::class, $value);

        $clonedValue->change(0);

        self::assertNotSame($some, $clone);
        self::assertNotSame($some->value(), $clone->value());

        self::assertEquals(42, $value->asInt());
        self::assertEquals(0, $clonedValue->asInt());
    }

    public function testCloningClonesValueWhenScalar(): void
    {
        $some = Some::from(42);
        $clone = clone $some;

        self::assertEquals(42, $some->value());
        self::assertEquals(42, $clone->value());
    }
}
