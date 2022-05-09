<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\TaggedMaybes;
use j45l\maybe\Context\Tags\StringTag;
use j45l\maybe\Context\Tags\Untagged;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Context\TaggedMaybes
 * @covers \j45l\maybe\Context\MaybeAware
 */
final class TaggedMaybesTest extends TestCase
{
    public function testEmptyOneHasNoActiveTag(): void
    {
        $taggedMaybes = TaggedMaybes::create();

        $this->assertInstanceOf(Untagged::class, $taggedMaybes->activeTag());
        $this->assertFalse($taggedMaybes->active());
    }

    public function testSettingOnEmptyOneHasEffect(): void
    {
        $taggedMaybes = TaggedMaybes::create();
        $taggedMaybesWithSome = $taggedMaybes->set(Some::from(42));

        $this->assertEquals($taggedMaybes, $taggedMaybesWithSome);
        $this->assertInstanceOf(Untagged::class, $taggedMaybesWithSome->activeTag());
        $this->assertFalse($taggedMaybesWithSome->active());
    }

    public function testATagCanBeSetAsTheActiveOne(): void
    {
        $taggedMaybes = TaggedMaybes::create();
        $taggedMaybes = $taggedMaybes->withTag(StringTag::create('tag'));

        $tag = $taggedMaybes->activeTag();
        $this->assertInstanceOf(StringTag::class, $tag);
        $this->assertEquals('tag', $tag->toString());
        $this->assertTrue($taggedMaybes->active());
    }

    public function testTaggingIsImmutable(): void
    {
        $taggedMaybes = TaggedMaybes::create();
        $taggedMaybesA = $taggedMaybes->withTag(StringTag::create('tag a'))->set(Some::from('A'));
        $taggedMaybesB = $taggedMaybes->withTag(StringTag::create('tag b'))->set(Some::from('B'));
        $taggedMaybesC = $taggedMaybes->withTag(StringTag::create('Should not mutate'));
        $taggedMaybesC->set(Some::from('Should not mutate'));

        $tagA = $taggedMaybesA->activeTag();
        $tagB = $taggedMaybesB->activeTag();
        $this->assertInstanceOf(Untagged::class, $taggedMaybes->activeTag());
        $this->assertInstanceOf(StringTag::class, $tagA);
        $this->assertInstanceOf(StringTag::class, $tagB);
        $this->assertEquals('tag a', $tagA->toString());
        $this->assertEquals('tag b', $tagB->toString());
        $this->assertEquals([], $taggedMaybesC->someValues());
        $this->assertEquals(['tag a' => 'A'], $taggedMaybesA->someValues());
        $this->assertEquals(['tag b' => 'B'], $taggedMaybesB->someValues());
        $this->assertTrue($taggedMaybesA->active());
        $this->assertTrue($taggedMaybesB->active());
        $this->assertFalse($taggedMaybes->active());
    }

    public function testTheLastTaggerMaybePerTagIsReturned(): void
    {
        $taggedMaybes = TaggedMaybes::create();
        $taggedMaybes = $taggedMaybes
            ->withTag(StringTag::create('tag'))
            ->set(Some::from('tag'))
            ->withTag(StringTag::create('another tag'))
            ->set(Some::from('first another tag'))
            ->set(Some::from('last another tag'))
        ;

        $tag = $taggedMaybes->activeTag();
        $this->assertInstanceOf(StringTag::class, $tag);
        $this->assertEquals('another tag', $tag->toString());
        $this->assertTrue($taggedMaybes->active());

        $this->assertEquals('tag', $taggedMaybes->someValues()['tag']);
        $this->assertEquals('last another tag', $taggedMaybes->someValues()['another tag']);
    }
}
