<?php

namespace j45l\maybe\Test\Unit\Tags;

use j45l\maybe\Context\Tags\TagCreator;
use j45l\maybe\Context\Tags\Untagged;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Context\Tags\TagCreator
 * @covers \j45l\maybe\Context\Tags\StringTag
 * @covers \j45l\maybe\Context\Tags\Untagged
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
