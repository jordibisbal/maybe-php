<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use Closure;
use j45l\either\Deferred;
use j45l\either\Either;
use j45l\either\None;
use j45l\either\Result\Failure;
use j45l\either\Result\Success;
use j45l\either\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\either\Either
 * @covers \j45l\either\Deferred
 */
final class PipeTest extends TestCase
{
    public function testResultIsPassToNextOne(): void
    {
        $addOne = $this->addOne();

        $either = Some::from(1)->pipe($addOne)->pipe($addOne);

        self::assertInstanceOf(Deferred::class, $either);

        $either = $either->resolve();

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(3, $either->get());
    }

    /** @noinspection PhpUnusedParameterInspection */

    private function addOne(): Closure
    {
        /** @param Some $either */
        return static function (Some $either): Some {
            return Some::from($either->get() + 1);
        };
    }

    public function testStopExecutionWhenNoneEnters(): void
    {
        $addOne = $this->addOne();
        $none = $this->none();

        $called = false;
        $notCalled = $this->notCalled($called);

        $either = Some::from(1)->pipe($addOne)->pipe($none)->pipe($notCalled);
        $either = $either->resolve();

        self::assertInstanceOf(None::class, $either);
        self::assertFalse($called);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function none(): Closure
    {
        return static function (Either $either): Either {
            return None::create();
        };
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings("unused")
     */
    private function notCalled(bool &$called): Closure
    {
        return static function (Either $either) use (&$called): Either {
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

        $either = Some::from(1)->pipe($addOne)->pipe($fails)->pipe($notCalled);
        $either = $either->resolve();

        self::assertInstanceOf(None::class, $either);
        self::assertInstanceOf(Failure::class, $either);
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
        return static function (Either $either): Either {
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
