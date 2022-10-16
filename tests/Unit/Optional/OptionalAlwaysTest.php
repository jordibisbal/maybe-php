<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalAlwaysTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $optional = Some::from(0)
            ->always(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $optional);
    }

    public function testSucceedReturnsLastValue(): void
    {
        $optional = JustSuccess::create()
            ->always(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $optional);
    }

    public function testNoneReturnsLastValue(): void
    {
        $optional = None::create()
            ->always(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $optional);
    }

    public function testFailureReturnsLastValue(): void
    {
        $optional = Failure::create()
            ->always(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $optional);
    }
}
