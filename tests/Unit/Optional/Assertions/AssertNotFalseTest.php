<?php

namespace j45l\maybe\Test\Unit\Optional\Assertions;

use Exception;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Assertions\AssertionFailed;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\assertNotFalse;

/** @covers ::j45l\maybe\Optional\assertNotFalse */
class AssertNotFalseTest extends TestCase
{
    /** @return array<mixed> */
    public function notFalseCases(): array
    {
        return [
            [Some::from(0)],
            [Some::from(42)],
            [None::create()],
            [JustSuccess::create()],
            [Failure::create()]
        ];
    }

    /**
     * @dataProvider notFalseCases
     * @param Optional<int> $subject
     * @throws Exception
     */
    public function testAssertNotFailsDoesNotFailForNotFalse(Optional $subject): void
    {
        self::assertSame($subject, assertNotFalse($subject));
    }

    /**
     * @throws Exception
     */
    public function testAssertFalseDoesFailForNotFalse(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('Failed asserting that a j45l\maybe\Maybe\Some is not false');

        assertNotFalse(Some::from(false));
    }

    /**
     * @throws Exception
     */
    public function testAssertFalseDoesNotFailForNotFalseWithMessage(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('message');

        assertNotFalse(Some::from(false), 'message');
    }

    /**
     * @throws Exception
     */
    public function testAssertFalseDoesNotFailForNotFalseWithThrowableMessage(): void
    {
        $exception = new RuntimeException('My Exception');
        $this->expectException($exception::class);
        $this->expectExceptionMessage('My Exception');

        assertNotFalse(Some::from(false), $exception);
    }
}
