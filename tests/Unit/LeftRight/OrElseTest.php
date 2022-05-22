<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\LeftRight\LeftRight
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OrElseTest extends TestCase
{
    public function testSinkResolves(): void
    {
        $addOne = $this->addOne();

        $maybe = Some::from(1)->orElse($addOne);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testFailureSinks(): void
    {
        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Failure::create()->orElse($isCalled);

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
        $failure = $this->failure();

        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Some::from(1)->andThen($failure)->orElse($isCalled);

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

        $maybe = Some::from(1)->andThen($fails)->orElse($isCalled);

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    public function testSinkCapturesFailures(): void
    {
        $captured = None::create();
        $captures = $this->capture($captured);
        $maybe = Some::from(1)->andThen($this->fails())->orElse($captures);

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertInstanceOf(Failure::class, $captured);

        $reason = $captured->reason();
        self::assertInstanceOf(ThrowableReason::class, $reason);

        self::assertEquals(new RuntimeException(), $reason->throwable());
    }

    public function testSinkCanReturnNone(): void
    {
        $failure = Failure::create();
        $some = $failure->orElse(static function () {
            return null;
        });

        self::assertInstanceOf(None::class, $some);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function failure(): Closure
    {
        return static function (LeftRight $maybe): LeftRight {
            return Failure::create();
        };
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param LeftRight<mixed> $result
     * @SuppressWarnings("unused")
     */
    private function isCalled(bool &$called, LeftRight $result): Closure
    {
        return static function (LeftRight $maybe) use (&$called, $result): LeftRight {
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
        return static function (LeftRight $maybe): LeftRight {
            throw new RuntimeException();
        };
    }

    /** @param mixed $captured */
    private function capture(&$captured): Closure
    {
        return function (LeftRight $maybe) use (&$captured) {
            $captured = $maybe;
            return $maybe;
        };
    }
}
