<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;
use function PHPUnit\Framework\assertEquals;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Either\Failure
 * @covers \j45l\maybe\Maybe\Some
 * @covers \j45l\maybe\Either\JustSuccess
 */
final class OptionalOrFailTest extends TestCase
{
    public function testSuccessOrFailDoesNotFail(): void
    {
        assertSomeEquals(42, Some::from(42)->orFail('Fail'));
        assertSuccess(JustSuccess::create()->orFail('Fail'));
    }

    public function testNoneOrFailFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail');
        $this->expectExceptionCode(0);

        None::create()->orFail('Fail');
    }

    public function testFailureOrFailFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail');
        $this->expectExceptionCode(0);

        Failure::create()->orFail('Fail');
    }

    public function testSomeGetOrFailDoesNotFail(): void
    {
        assertEquals(42, Some::from(42)->getOrFail('Fail'));
    }

    public function testJustSuccessGetOrFailDoesFail(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail');
        $this->expectExceptionCode(0);

        JustSuccess::create()->getOrFail('Fail');
    }

    public function testNoneGetOrFailFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail');
        $this->expectExceptionCode(0);

        None::create()->getOrFail('Fail');
    }

    public function testFailureGetOrFailFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Fail');
        $this->expectExceptionCode(0);

        Failure::create()->getOrFail('Fail');
    }
}
