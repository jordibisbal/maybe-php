<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\Trail;
use j45l\maybe\None;
use j45l\maybe\Result\Failure;
use j45l\maybe\Result\Reason;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;

use function Functional\invoke;

/** @covers \j45l\maybe\Context\Trail */
final class TrailTest extends TestCase
{
    public function testPushingToATrailDoesNotModifyIt(): void
    {
        $trail = Trail::create();
        $secondTrail = $trail->push(None::create());

        self::assertCount(0, $trail->asArray());
        self::assertCount(1, $secondTrail->asArray());
    }

    public function testGettingAllDoesNotReturnNoneMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Some::from(2))
        ;

        self::assertEquals([1, 2], $trail->values());
    }

    public function testGettingButLastDoesNotReturnLastMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->values());
    }

    public function testGettingLastReturnsLastOne(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([Some::from(3)], $trail->last()->asArray());
    }

    public function testGettingLastFromEmptyTrailReturnsEmptyTrail(): void
    {
        self::assertTrue(Trail::create()->last()->empty());
    }

    public function testButLastDoesNotModifyTrail(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->values());
        self::assertEquals([1, 2, 3], $trail->values());
    }

    public function testGettingFailureDoesNotReturnOtherMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Failure::from(Reason::fromString('failed')))
        ;

        self::assertCount(1, $trail->failed());
        self::assertEquals('failed', $trail->failed()[0]->reason()->toString());
    }

    public function testResolvingASomeDoesNotAddsToTheTrail(): void
    {
        $some = Some::from(42)->resolve()->resolve()->resolve();

        self::assertCount(1, $some->trail());
    }

    public function testCanBeCheckForEmptiness(): void
    {
        self::assertTrue(Trail::create()->empty());
    }

    public function testFailuresReasons(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42))
            ->push(Failure::from(Reason::fromString('because failed')))
        ;

        self::assertEquals(
            ['because failed'],
            invoke($trail->failureReasons(), 'toString')
        );
    }
}
