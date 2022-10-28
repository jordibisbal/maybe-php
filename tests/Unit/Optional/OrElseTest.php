<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertFailureReasonThrowableOf;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OrElseTest extends TestCase
{
    public function testSinkResolves(): void
    {
        $addOne = $this->addOne();

        $maybe = Some::from(1)->orElse($addOne);

        assertSomeEquals(1, $maybe);
    }

    public function testFailureSinks(): void
    {
        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Failure::create()->orElse($isCalled);

        assertSomeEquals(42, $maybe);
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

        assertSomeEquals(42, $maybe);
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    public function testStopExecutionWhenFailureEntersThenSink(): void
    {
        $fails = $this->fails();

        $called = false;
        $isCalled = $this->isCalled($called, Some::from(42));

        $maybe = Some::from(1)->andThen($fails)->orElse($isCalled);

        assertSomeEquals(42, $maybe);
        /** @noinspection PhpUnitAssertTrueWithIncompatibleTypeArgumentInspection */
        self::assertTrue($called);
    }

    public function testSinkCapturesFailures(): void
    {
        $captured = None::create();
        $captures = $this->capture($captured);
        $maybe = Some::from(1)->andThen($this->fails())->orElse($captures);

        assertFailure($maybe);
        assertFailure($captured);

        assertFailureReasonThrowableOf(RuntimeException::class, $captured);
    }

    public function testSinkCanReturnNone(): void
    {
        $failure = Failure::create();
        $some = $failure->orElse(static function () {
            return null;
        });

        assertNone($some);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function failure(): Closure
    {
        return static function (Optional $maybe): Optional {
            return Failure::create();
        };
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param Optional<mixed> $result
     * @SuppressWarnings("unused")
     */
    private function isCalled(bool &$called, Optional $result): Closure
    {
        return static function (Optional $maybe) use (&$called, $result): Optional {
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
        return static function (Optional $maybe): Optional {
            throw new RuntimeException();
        };
    }

    /** @param mixed $captured */
    private function capture(mixed &$captured): Closure
    {
        return static function (Optional $maybe) use (&$captured) {
            $captured = $maybe;
            return $maybe;
        };
    }
}
