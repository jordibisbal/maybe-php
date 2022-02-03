<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Failure;
use j45l\either\None;
use j45l\either\Reason;
use j45l\either\Some;
use j45l\either\Tag;
use j45l\either\Trail;
use PHPUnit\Framework\TestCase;
use function Functional\invoke;

/** @covers \j45l\either\Trail */
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

    public function testGettingLastReturnsLastOne(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([Some::from(3)], $trail->justLast()->asArray());
    }

    public function testGettingLastFromEmptyTrailReturnsEmptyTrail(): void
    {
        self::assertTrue(Trail::create()->justLast()->empty());
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

    public function testGettingFailureDoesNotReturnOtherEither(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Failure::from(Reason::from('failed')))
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
        self::assertTrue(Trail::create()->empty());
    }

    public function testCanPushEitherTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), Tag::from('42'))
            ->push(Some::from(43), Tag::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->getTaggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->getTagged());
    }

    public function testTaggedValuesJustForSome(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), Tag::from('42'))
            ->push(None::create(), Tag::from('43'))
        ;

        self::assertEquals(['42' => 42], $trail->getTaggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => None::create()], $trail->getTagged());
    }

    public function testTaggedFailuresValuesJustForFailures(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), Tag::from('42'))
            ->push(Failure::from(Reason::from('because failed')), Tag::from('43'))
        ;

        self::assertEquals(
            ['43' => 'because failed'],
            invoke($trail->getTaggedFailureReasons(), 'asString')
        );
        self::assertEquals(
            ['42' => Some::from('42'), '43' => Failure::from(Reason::from('because failed'))],
            $trail->getTagged()
        );
    }
}
