<?php

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\Context;
use j45l\maybe\Context\Parameters;
use j45l\maybe\Context\Tags\StringTag;
use j45l\maybe\Context\Tags\TagCreator;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Context\Context */
class ContextTest extends TestCase
{
    public function testAContextCanBeBuildFromParameters(): void
    {
        self::assertEquals(
            Context::create()->withParameters(1, 2, 3),
            Context::fromParameters(Parameters::create(1, 2, 3))
        );

        self::assertEquals(
            Context::create()->withParameters(1, 2, 3)->parameters(),
            Context::fromParameters(Parameters::create(1, 2, 3))->parameters()
        );
    }

    public function testAnMaybeCanBePushIntoAContextTailAsNewContext(): void
    {
        $some = Some::from(null);
        $context = Context::create();

        $newContext = $context->push($some);

        self::assertNotSame($newContext, $context);
        self::assertTrue($context->trail()->empty());

        self::assertSame([$some], $newContext->trail()->asArray());
    }

    public function testAContextCanApplyATag(): void
    {
        $context = Context::create()
            ->withTag(StringTag::create('tag'))
            ->tag(Some::from(42))
        ;

        self::assertEquals(['tag' => 42], $context->tagged()->someValues());
    }

    public function testAContextCanApplyATagSeveralTimesToTags(): void
    {
        $context = Context::create()
            ->withTag(StringTag::create('tag 43'))
            ->tag(Some::from(42))
            ->tag(Some::from(43))
            ->withTag(StringTag::create('tag 44'))
            ->tag(Some::from(44))
        ;

        self::assertEquals(['tag 43' => 43, 'tag 44' => 44], $context->tagged()->someValues());
        self::assertEquals(TagCreator::from('tag 44'), $context->tagged()->activeTag());
    }
}
