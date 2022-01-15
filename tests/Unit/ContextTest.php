<?php

namespace j45l\either\Test\Unit;

use j45l\either\Context;
use j45l\either\Parameters;
use j45l\either\Some;
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
        $some = Some::create();
        $context = Context::create();

        $newContext = $context->push($some);

        self::assertNotSame($newContext, $context);
        self::assertTrue($context->trail()->empty());

        self::assertSame([$some], $newContext->trail()->asArray());
    }
}
