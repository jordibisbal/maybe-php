<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotFailure;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\reason;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalBindTest extends TestCase
{
    public function testMapSomeReturnsItsValueMapped(): void
    {
        $addOne = static function (int $x): int {
            return $x + 1;
        };

        $maybe = Some::from(41)->bind($addOne);

        assertSomeEquals(42, $maybe);
    }

    public function testMapNoneReturnsNoneMapped(): void
    {
        $addOne = static function (Some $some): Optional {
            return Some::from($some->get() + 1);
        };

        $maybe = None::create()->bind($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapNoneDoesNotResolveDeferred(): void
    {
        $addOne = static function (Optional $maybe): Optional {
            throw new RuntimeException('Should not be resolved');
        };

        $maybe = None::create()->bind($addOne);

        assertNone($maybe);
        assertNotFailure($maybe);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testMapThrowsException(): void
    {
        $fails = static function (): Optional {
            throw new RuntimeException('Failure');
        };

        $failure = Some::from(42)->bind($fails);

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals('Failure', reason($failure)->toString());
    }
}
