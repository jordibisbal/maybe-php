<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;
use function j45l\maybe\Optional\safe;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 */
final class OptionalAndThenTest extends TestCase
{
    public function testSomeReturnsLastValue(): void
    {
        $maybe = Some::from(0)
            ->andThen(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $maybe);
    }

    public function testSucceedReturnsLastValue(): void
    {
        $maybe = JustSuccess::create()
            ->andThen(function (): Optional {
                return Some::from(42);
            })
        ;

        assertSomeEquals(42, $maybe);
    }

    public function testNoneReturnsNoneValue(): void
    {
        $maybe = None::create()->andThen(Some::from(42));

        assertNone($maybe);
    }

    public function testDeferredSomeReturnsNextValue(): void
    {
        $maybe =
            safe(static function (): Optional {
                return Some::from(42);
            })->andThen(None::create())
        ;

        assertNone($maybe);
    }

    public function testResultIsPassToNextOne(): void
    {
        $addOne = $this->addOne();

        $some = Some::from(1)->andThen($addOne)->andThen($addOne);

        assertSomeEquals(3, $some);
    }

    /** @noinspection PhpUnusedParameterInspection */

    private function addOne(): Closure
    {
        /** @param Some $maybe */
        return static function (Some $maybe): Some {
            return Some::from($maybe->get() + 1);
        };
    }

    public function testStopExecutionWhenFailureEnters(): void
    {
        $addOne = $this->addOne();
        $failure = $this->failure();

        $called = false;
        $notCalled = $this->notCalled($called);

        $maybe = Some::from(1)->andThen($addOne)->andThen($failure)->andThen($notCalled);

        assertFailure($maybe);
        self::assertFalse($called);
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
     * @SuppressWarnings("unused")
     */
    private function notCalled(bool &$called): Closure
    {
        return static function (Optional $maybe) use (&$called): Optional {
            $called = true;

            return None::create();
        };
    }

    public function testStopExecutionWhenSomeFails(): void
    {
        $addOne = $this->addOne();
        $fails = $this->fails();

        $called = false;
        $notCalled = $this->notCalled($called);

        $maybe = Some::from(1)->andThen($addOne)->andThen($fails)->andThen($notCalled);

        assertFailure($maybe);
        self::assertFalse($called);
    }

    public function testDeferredResolvesOnPipe(): void
    {
        $isSome = Some::from(1)
            ->andThen($this->identity())
            ->andThen($this->isSome())
        ;

        assertSuccess($isSome);
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

    private function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }

    private function isSome(): Closure
    {
        return static function ($value) {
            return $value instanceof Some ? JustSuccess::create() : Failure::create();
        };
    }
}
