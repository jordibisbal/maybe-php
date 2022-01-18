<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\None;
use j45l\either\Some;
use j45l\either\Tag;
use j45l\either\Trail;
use j45l\either\Untagged;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\either\Trail
 * @covers \j45l\either\Tag
 * @covers \j45l\either\StringTag
 * @covers \j45l\either\Untagged
 */
final class TrailTagTest extends TestCase
{
    public function testCanPushEitherTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), Tag::from('42'))
            ->push(Some::from(43), Tag::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->getTaggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->getTagged());
    }

    public function testCanPushEitherUnTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), Tag::from('42'))
            ->push(Some::from(43), Tag::from('43'))
            ->push(Some::from(44), Tag::untagged())
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

    public function testCreatingAnEmptyTagResultsInAnUntagged(): void
    {
        self::assertInstanceOf(Untagged::class, Tag::from(''));
    }
}
