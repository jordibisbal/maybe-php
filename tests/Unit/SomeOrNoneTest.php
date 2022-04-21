<?php

namespace j45l\maybe\Test\Unit;

use Closure;
use j45l\maybe\Deferred;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\lift;
use function j45l\maybe\someOrNone;

/** @covers ::j45l\maybe\someOrNone */
class SomeOrNoneTest extends TestCase
{
    public function testsOnNoneReturnsNone(): void
    {
        self::assertInstanceOf(None::class, someOrNone(None::create()));
    }

    public function testsOnNullReturnsNone(): void
    {
        self::assertInstanceOf(None::class, someOrNone(Some::from(null)));
    }

    public function testsOnValueReturnsSome(): void
    {
        $some = someOrNone(Some::from(42));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnDeferredValueReturnsSome(): void
    {
        $some = someOrNone(Deferred::create(function () {
            return 42;
        }));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }

    public function testsOnDeferredNullReturnsNone(): void
    {
        $some = someOrNone(Deferred::create(function () {
            return 42;
        }));
        self::assertInstanceOf(Some::class, $some);
        self::assertEquals(42, $some->get());
    }
}
