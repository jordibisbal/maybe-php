<?php

namespace j45l\either\Test\Unit\Tags;

use j45l\either\TagCreator;
use j45l\either\Tags\Untagged;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\either\TagCreator
 * @covers \j45l\either\Tags\StringTag
 * @covers \j45l\either\Tags\Untagged
 */
class TagCreatorTest extends TestCase
{
    public function testTagCreatedFromTagReturnsOriginalTag(): void
    {
        $tag = TagCreator::from(42);

        $this->assertEquals($tag, TagCreator::from($tag));
        $this->assertSame($tag, TagCreator::from($tag));
    }

    public function testCreatingTagFromEmptyStringResultsInUntagged(): void
    {
        $tag = TagCreator::from('');

        $this->assertInstanceOf(Untagged::class, $tag);
        $this->assertEquals(Untagged::create(), $tag);
    }
}
