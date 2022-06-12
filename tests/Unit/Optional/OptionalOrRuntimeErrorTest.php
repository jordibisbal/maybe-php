<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 * @covers \j45l\maybe\Either\Failure
 * @covers \j45l\maybe\Either\JustSuccess
 */
final class OptionalOrRuntimeErrorTest extends TestCase
{
    public function testSomeReturnsItsValue(): void
    {
        self::assertEquals(42, Some::from(42)->getOrRuntimeException());
    }

    public function testNoneThrowsRuntimeError(): void
    {
        $this->expectExceptionObject(new RuntimeException('Ops'));

        None::create()->getOrRuntimeException('Ops');
    }

    public function testJustSuccessThrowsRuntimeError(): void
    {
        $this->expectExceptionObject(new RuntimeException('Ops'));

        JustSuccess::create()->getOrRuntimeException('Ops');
    }

    public function testFailureThrowsRuntimeError(): void
    {
        $this->expectExceptionObject(new RuntimeException('Ops'));

        Failure::create()->getOrRuntimeException('Ops');
    }

    public function testThrowableFailureThrowsRuntimeError(): void
    {
        try {
            Failure::because(ThrowableReason::fromString('Inner Runtime'))->getOrRuntimeException('Ops');
        } catch (Throwable $throwable) {
            $this->assertEquals(
                new RuntimeException(
                    'Ops',
                    0,
                    new RuntimeException('Inner Runtime')
                ),
                $throwable
            );
        }
    }

    public function testThrowableFailureThrowsRuntimeErrorWithNoMessage(): void
    {
        try {
            Failure::because(ThrowableReason::fromString('Runtime'))->getOrRuntimeException();
        } catch (Throwable $throwable) {
            $this->assertEquals(
                new RuntimeException(
                    'Runtime',
                    0,
                    new RuntimeException('Runtime')
                ),
                $throwable
            );
        }
    }
}
