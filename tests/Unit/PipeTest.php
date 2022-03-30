<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Maybe
 * @covers \j45l\maybe\Deferred
 */
final class PipeTest extends TestCase
{
    public function testResultIsPassToNextOne(): void
    {
        $addOne = $this->addOne();

        $maybe = Some::from(1)->pipe($addOne)->pipe($addOne);

        self::assertInstanceOf(Deferred::class, $maybe);

        $maybe = $maybe->resolve();

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(3, $maybe->get());
    }

    /** @noinspection PhpUnusedParameterInspection */

    private function addOne(): Closure
    {
        /** @param Some $maybe */
        return static function (Some $maybe): Some {
            return Some::from($maybe->get() + 1);
        };
    }

    public function testStopExecutionWhenNoneEnters(): void
    {
        $addOne = $this->addOne();
        $none = $this->none();

        $called = false;
        $notCalled = $this->notCalled($called);

        $maybe = Some::from(1)->pipe($addOne)->pipe($none)->pipe($notCalled);
        $maybe = $maybe->resolve();

        self::assertInstanceOf(None::class, $maybe);
        self::assertFalse($called);
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
     * @SuppressWarnings("unused")
     */
    private function notCalled(bool &$called): Closure
    {
        return static function (Maybe $maybe) use (&$called): Maybe {
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

        $maybe = Some::from(1)->pipe($addOne)->pipe($fails)->pipe($notCalled);
        $maybe = $maybe->resolve();

        self::assertInstanceOf(None::class, $maybe);
        self::assertInstanceOf(Failure::class, $maybe);
        self::assertFalse($called);
    }

    public function testDeferredResolvesOnPipe(): void
    {
        $assertIsSome = Some::from(1)
            ->pipe($this->identity())
            ->pipe($this->isSome())
        ;

        self::assertInstanceOf(Success::class, $assertIsSome->resolve());
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

    private function identity(): Closure
    {
        return static function ($value) {
            return $value;
        };
    }

    private function isSome(): Closure
    {
        return static function ($value) {
            return $value instanceof Some ? Success::create() : Failure::create();
        };
    }
}
