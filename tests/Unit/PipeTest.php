<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit;

use Closure;
use j45l\either\Deferred;
use j45l\either\Either;
use j45l\either\Failed;
use j45l\either\None;
use j45l\either\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PipeTest extends TestCase
{
    public function testResultIsPassToNextOne(): void
    {
        $addOne = $this->addOne();

        $either = Some::from(1)->pipe($addOne)->pipe($addOne);

        self::assertInstanceOf(Deferred::class, $either);

        $either = $either->resolve();

        self::assertInstanceOf(Some::class, $either);
        self::assertEquals(3, $either->value());
    }

    /** @noinspection PhpUnusedParameterInspection */

    private function addOne(): Closure
    {
        /** @param Some $either */
        return static function (Either $either): Either {
            return Some::from($either->value() + 1);
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
        self::assertInstanceOf(Failed::class, $either);
        self::assertFalse($called);
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
}
