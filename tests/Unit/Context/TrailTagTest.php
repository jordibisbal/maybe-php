<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\Trail;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Reason;
use j45l\maybe\Some;
use j45l\maybe\Tags\TagCreator;
use j45l\maybe\Tags\Untagged;
use PHPUnit\Framework\TestCase;

use function Functional\invoke;

/**
 * @covers \j45l\maybe\Context\Trail
 * @covers \j45l\maybe\Tags\StringTag
 * @covers \j45l\maybe\Tags\Untagged
 */
final class TrailTagTest extends TestCase
{
    public function testCanPushMaybeTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testCanPushMaybeUnTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
            ->push(Some::from(44), new Untagged())
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testTaggedValuesJustForSome(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(None::create(), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => None::create()], $trail->tagged());
    }

    public function testCreatingAnEmptyTagResultsInAnUntagged(): void
    {
        self::assertInstanceOf(Untagged::class, TagCreator::from(''));
    }

    public function testCanPushTaggedMaybe(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testTaggedFailuresReasons(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Failure::from(Reason::fromString('because failed')), TagCreator::from('43'))
        ;

        self::assertEquals(
            ['43' => 'because failed'],
            invoke($trail->taggedFailureReasons(), 'toString')
        );
        self::assertEquals(
            ['42' => Some::from('42'), '43' => Failure::from(Reason::fromString('because failed'))],
            $trail->tagged()
        );
    }
}
