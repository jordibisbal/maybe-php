<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Failed;
use j45l\either\None;
use j45l\either\Reason;
use j45l\either\Some;
use j45l\either\Trail;
use PHPUnit\Framework\TestCase;

final class TrailTest extends TestCase
{
    public function testPushingToATrailDoesNotModifyIt(): void
    {
        $trail = Trail::create();
        $secondTrail = $trail->push(None::create());

        self::assertCount(0, $trail->asArray());
        self::assertCount(1, $secondTrail->asArray());
    }

    public function testGettingAllDoesNotReturnNoneEither(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Some::from(2))
        ;

        self::assertEquals([1, 2], $trail->getValues());
    }

    public function testGettingButLastDoesNotReturnLastEither(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->getValues());
    }

    public function testButLastDoesNotModifyTrail(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->getValues());
        self::assertEquals([1, 2, 3], $trail->getValues());
    }

    public function testGettingFailedDoesNotReturnOtherEither(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Failed::from(Reason::from('failed')))
        ;

        self::assertCount(1, $trail->failed());
        self::assertEquals('failed', $trail->failed()[0]->reason()->asString());
    }

    public function testResolvingASomeDoesNotAddsToTheTrail(): void
    {
        $some = Some::from(42)->resolve()->resolve()->resolve();

        self::assertCount(1, $some->trail());
    }

    public function testCanBeCheckForEmptiness(): void
    {
        self::assertTrue(Trail::create()->isEmpty());
    }
}
