<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\DoTry\ThrowableReason;
use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\Deferred
 * @covers \j45l\maybe\Some
 */
final class SinkTest extends TestCase
{
    public function testSinkResolves(): void
    {
        $addOne = $this->addOne();

        $maybe = Some::from(1)->sink($addOne);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testNoneSinks(): void
    {
        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = None::create()->sink($isCalled);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    /** @noinspection PhpUnusedParameterInspection */

    private function addOne(): Closure
    {
        /** @param Some $maybe */
        return static function (Some $maybe): Some {
            return Some::from($maybe->get() + 1);
        };
    }

    public function testStopExecutionWhenNoneEntersThenSink(): void
    {
        $none = $this->none();

        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Some::from(1)->pipe($none)->sink($isCalled);
        $maybe = $maybe->resolve();

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    public function testStopExecutionWhenFailureEntersThenSink(): void
    {
        $fails = $this->fails();

        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Some::from(1)->pipe($fails)->sink($isCalled);
        $maybe = $maybe->resolve();

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    public function testSinkCapturesFailures(): void
    {
        $captured = None::create();
        $captures = $this->capture($captured);
        $maybe = Some::from(1)->pipe($this->fails())->sink($captures);

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertInstanceOf(Failure::class, $captured);

        $reason = $captured->reason();
        self::assertInstanceOf(ThrowableReason::class, $reason);

        self::assertEquals(new RuntimeException(), $reason->throwable());
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function none(): Closure
    {
        return static function (Maybe $maybe): Maybe {
            return None::create();
        };
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param Maybe<mixed> $result
     * @SuppressWarnings("unused")
     */
    private function isCalled(bool &$called, Maybe $result): Closure
    {
        return static function (Maybe $maybe) use (&$called, $result): Maybe {
            $called = true;

            return $result;
        };
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function fails(): Closure
    {
        return static function (Maybe $maybe): Maybe {
            throw new RuntimeException();
        };
    }

    /** @param mixed $captured */
    private function capture(&$captured): Closure
    {
        return function (Maybe $maybe) use (&$captured) {
            $captured = $maybe;
            return $maybe;
        };
    }
}
