<?php

namespace j45l\maybe\Test\Unit\Either;

use Closure;
use j45l\functional\Sequences\ExponentialSequence;
use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Functions\retry;

/** @covers ::j45l\maybe\Functions\retry */
class RetryTest extends TestCase
{
    public function testTriesAsManyTimesAsAskedFor(): void
    {
        $tries = 0;
        $delaySequence = [];

        $failure = retry(
            $this->alwaysFailing($tries),
            3,
            ExponentialSequence::create(2, 5),
            $this->delay($delaySequence)
        );

        self::assertInstanceOf(Failure::class, $failure);
        self::assertEquals('Runtime exception 3', $failure->reason()->toString());
        self::assertEquals(3, $tries);
        self::assertEquals([5.0, 10.0], $delaySequence);
    }

    public function testTriesUntilNone(): void
    {
        $tries = 0;
        $delaySequence = [];
        $success = retry(
            $this->failingTwiceThenNone($tries),
            3,
            ExponentialSequence::create(2, 5),
            $this->delay($delaySequence)
        );

        self::assertInstanceOf(None::class, $success);
        self::assertEquals(2, $tries);
        self::assertEquals([5.0], $delaySequence);
    }

    public function testTriesUntilSucceeds(): void
    {
        $tries = 0;
        $delaySequence = [];
        $success = retry(
            $this->failingTwiceThenSome($tries),
            3,
            ExponentialSequence::create(2, 5),
            $this->delay($delaySequence)
        );

        self::assertInstanceOf(Some::class, $success);
        self::assertEquals(2, $tries);
        self::assertEquals([5.0], $delaySequence);
    }

    /** @param array<int> $delaySequence */
    private function delay(array &$delaySequence): Closure
    {
        return function ($delay) use (&$delaySequence) {
            $delaySequence[] = $delay;
        };
    }

    private function alwaysFailing(int &$tries): Closure
    {
        return function () use (&$tries): void {
            $tries++;
            throw new RuntimeException('Runtime exception ' . $tries);
        };
    }

    private function failingTwiceThenNone(int &$tries): Closure
    {
        return function () use (&$tries): void {
            $tries++;
            if ($tries == 2) {
                return;
            }
            throw new RuntimeException('Runtime exception ' . $tries);
        };
    }

    private function failingTwiceThenSome(int &$tries): Closure
    {
        return function () use (&$tries): int {
            $tries++;
            if ($tries == 2) {
                return 42;
            }
            throw new RuntimeException('Runtime exception ' . $tries);
        };
    }
}
