<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Maybe;

use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Maybe\Some */
final class SomeTest extends TestCase
{
    public function testCloningClonesValueWhenScalar(): void
    {
        $some = Some::from(42);
        $clone = clone $some;

        self::assertEquals(42, $some->get());
        self::assertEquals(42, $clone->get());
    }

    public function testGetOrElse(): void
    {
        self::assertEquals(42, Some::from(42)->getOrElse(null));
    }

    public function testTakeOrElse(): void
    {
        self::assertEquals(42, Some::from(['answer' => 42])->takeOrElse('answer', null));
    }

    public function testTakeOrElseNotFound(): void
    {
        self::assertEquals(
            'unknown',
            Some::from(['question' => 42])->takeOrElse('answer', 'unknown')
        );
    }
}
