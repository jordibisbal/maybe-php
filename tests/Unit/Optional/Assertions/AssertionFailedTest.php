<?php

namespace j45l\maybe\Test\Unit\Optional\Assertions;

use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Assertions\AssertionFailed;
use PHPUnit\Framework\TestCase;

/** @covers \j45l\maybe\Optional\Assertions\AssertionFailed */
class AssertionFailedTest extends TestCase
{
    public function testCreateGenericWithMessage(): void
    {
        $exception = AssertionFailed::because('Message');

        self::assertEquals('Message', $exception->getMessage());
    }

    public function testOptionalNotFalse(): void
    {
        $exception = AssertionFailed::becauseOptionalIsNotFalse(Some::from(false));

        self::assertEquals(
            'Failed asserting that a j45l\maybe\Maybe\Some is not false.',
            $exception->getMessage()
        );
    }
}
