<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Success;
use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\LeftRight\LeftRight
 */
final class AndThenTest extends TestCase
{
    public function testResultIsPassToNextOne(): void
    {
        $addOne = $this->addOne();

        $some = Some::from(1)->andThen($addOne)->andThen($addOne);

        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(3, $some->get());
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

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertFalse($called);
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
     * @SuppressWarnings("unused")
     */
    private function notCalled(bool &$called): Closure
    {
        return static function (LeftRight $maybe) use (&$called): LeftRight {
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

        self::assertInstanceOf(Failure::class, $maybe);
        self::assertFalse($called);
    }

    public function testDeferredResolvesOnPipe(): void
    {
        $isSome = Some::from(1)
            ->andThen($this->identity())
            ->andThen($this->isSome())
        ;

        self::assertInstanceOf(Success::class, $isSome);
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
