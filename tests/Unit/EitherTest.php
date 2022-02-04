<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use j45l\either\Either;
use j45l\either\None;
use j45l\either\Some;
use j45l\either\Tags\TagCreator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\None
 * @covers \j45l\either\Some
 * @covers \j45l\either\Failure
 * @covers \j45l\either\Deferred
 */
final class EitherTest extends TestCase
{
    public function testAnEitherHaveAContextWithParameters(): void
    {
        $either = None::create();
        $eitherWitContext = $either->with(1, 2, 3);

        self::assertEquals([], $either->context()->parameters()->asArray());
        self::assertEquals([1, 2, 3], $eitherWitContext->context()->parameters()->asArray());
    }

    public function testAfterChangingContextTrailIsNotLost(): void
    {
        $either = Some::from(1)->then(2)->then(3);
        $trail = $either->trail();

        $either = $either->with(None::create());

        self::assertEquals($trail->butLast(), $either->trail()->butLast());
    }

    public function testNextWithEitherTrailIsNotLost(): void
    {
        $either = Some::from(1)->then(2)->then(3);
        $trail = $either->trail();

        $either = $either->next(None::create());

        self::assertEquals($trail->butLast(), $either->trail()->butLast()->butLast());
    }

    public function testTrailWithNewTagIsSet(): void
    {
        $either = Either::start()->withTag('tag');

        $this->assertEquals(TagCreator::from('tag'), $either->context()->tag());
    }
}
