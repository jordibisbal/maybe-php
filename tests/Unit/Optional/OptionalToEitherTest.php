<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Either\Either
 */
final class OptionalToEitherTest extends TestCase
{
    public function testFailureIsItself(): void
    {
        $failure = Failure::create();
        self::assertSame($failure, $failure->toEither());
    }

    public function testJustSuccessIsItself(): void
    {
        $failure = JustSuccess::create();
        self::assertSame($failure, $failure->toEither());
    }

    public function testSomeReturnsJustSuccess(): void
    {
        self::assertInstanceOf(JustSuccess::class, Some::from(42)->toEither());
    }

    public function testNoneReturnsFailure(): void
    {
        $failure = None::create()->toEither();

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals(sprintf('From %s', None::class), $failure->reason()->toString());
    }
}
