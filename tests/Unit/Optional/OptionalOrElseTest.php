<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\functional\value;
use function j45l\maybe\Optional\PhpUnit\assertJustSuccess;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalOrElseTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        $maybe = Some::from(42)->orElse(value(0));

        assertSomeEquals(42, $maybe);
    }

    public function testSucceedReturnsItself(): void
    {
        $maybe = JustSuccess::create()->orElse(value(0));

        assertJustSuccess($maybe);
    }

    public function testNoneReturnsDefaultValue(): void
    {
        $maybe = None::create()->OrElse(value(Some::from(42)));

        assertSomeEquals(42, $maybe);
    }

    public function testDeferredSomeReturnsItsValue(): void
    {
        $fortyTwo = static function (): Optional {
            return Some::from(42);
        };

        $maybe = Either::try($fortyTwo)->OrElse(value(None::create()));

        assertSomeEquals(42, $maybe);
    }

    public function testDeferredNoneReturnsDefaultValue(): void
    {
        $none = static function (): Optional {
            return None::create();
        };

        $maybe = Either::try($none)->OrElse(value(Some::from(42)));

        assertSomeEquals(42, $maybe);
    }
}
