<?php

namespace j45l\either\Test\Unit;

use j45l\either\Context;
use j45l\either\Parameters;
use j45l\either\Some;
use j45l\either\Tag;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\either\Context */
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

    public function testAnEitherCanBePushIntoAContextTailAsNewContext(): void
    {
        $some = Some::from(null);
        $context = Context::create();

        $newContext = $context->push($some);

        self::assertNotSame($newContext, $context);
        self::assertTrue($context->trail()->empty());

        self::assertSame([$some], $newContext->trail()->asArray());
    }

    public function testAContextCanApplyATagToTrail(): void
    {
        $context = Context::create()
            ->withTag(Tag::from('tag'))
            ->push(Some::from(42))
        ;

        self::assertEquals(['tag' => 42], $context->trail()->getTaggedValues());
    }

    public function testAContextCanApplyATagVariousTimeToTrail(): void
    {
        $context = Context::create()
            ->withTag(Tag::from('tag 43'))
            ->push(Some::from(42))
            ->push(Some::from(43))
            ->withTag(Tag::from('tag 44'))
            ->push(Some::from(44))
        ;

        self::assertEquals(['tag 43' => 43, 'tag 44' => 44], $context->trail()->getTaggedValues());
    }
}
