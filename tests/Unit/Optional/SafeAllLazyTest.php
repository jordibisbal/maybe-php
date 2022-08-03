<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optionals;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function j45l\maybe\Optional\safeAllLazy;

/** @covers ::j45l\maybe\Optional\safeAllLazy */
class SafeAllLazyTest extends TestCase
{
    public function testSafeAllReturnsOptionals(): void
    {
        $called = false;

        $optionals = safeAllLazy([
            'f41' => function () {
                return 41;
            },
            'f42' => function () {
                return Some::from(42);
            },
            'null' => null,
            '42' => 42,
            'exception' => function () use (&$called) {
                $called = true;

                throw new RuntimeException();
            }
        ]);

        $this->assertFalse($called);
        $this->assertEquals(
            Optionals::create([
                'f41' => Some::from(41),
                'f42' => Some::from(42),
                'null' => None::create(),
                '42' => Some::from(42),
                'exception' => Failure::because(ThrowableReason::fromThrowable(new RuntimeException()))
            ]),
            $optionals()
        );

        /** @phpstan-ignore-next-line */
        $this->assertTrue($called);
    }
}
