<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalMapTest extends TestCase
{
    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (int $x): int {
            return $x + 1;
        };

        $maybe = Some::from(41)->map($addOne);

        assertSomeEquals(42, $maybe);
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): Optional {
            return Some::from($some->get() + 1);
        };

        $maybe = None::create()->map($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (Optional $maybe): Optional {
            throw new RuntimeException('Should not be resolved');
        };

        $maybe = None::create()->map($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapThrowsException(): void
    {
        $fails = static function (): Optional {
            throw new RuntimeException();
        };

        $this->expectException(RuntimeException::class);
        Some::from(42)->map($fails);
    }
}
