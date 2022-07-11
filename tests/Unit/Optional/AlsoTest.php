<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\also;
use function j45l\maybe\Optional\PhpUnit\assertSome;

/** @covers ::j45l\maybe\Optional\also */
class AlsoTest extends TestCase
{
    public function testsAlsoProducesEffect(): void
    {
        $value = 0;
        $effect = static function (Optional $optional) use (&$value) {
            $value = $optional->getOrElse(0) + 1;
            return None::create();
        };

        $original = Some::from(41);
        $some = $original->andThen(also($effect));

        self::assertSame($original, $some);
        self::assertEquals(42, $value);
    }

    public function testsAlsoWithParametersProducesEffect(): void
    {
        $value = 0;
        $effect = static function (Optional $optional, $added) use (&$value) {
            $value = $optional->getOrElse(0) + $added;
            return None::create();
        };

        $original = Some::from(41);
        $some = $original->andThen(also($effect, 1));

        self::assertSame($original, $some);
        self::assertEquals(42, $value);
    }

    public function testsCanChangeOptionalByFailing(): void
    {
        $effect = static function () {
            throw new RuntimeException('Failed');
        };

        $original = Some::from(41);
        $error = $original->andThen(also($effect));

        assertSome($original);
        self::assertInstanceOf(Failure::class, $error);

        $reason = $error->reason();
        self::assertInstanceOf(ThrowableReason::class, $reason);
        self::assertInstanceOf(RuntimeException::class, $reason->throwable());
        self::assertEquals('Failed', $reason);
    }
}
