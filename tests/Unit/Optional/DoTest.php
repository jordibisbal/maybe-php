<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\PhpUnit\assertFailureReasonString;
use function j45l\maybe\Optional\PhpUnit\assertNone;
use function j45l\maybe\Optional\PhpUnit\assertNotSuccess;
use function j45l\maybe\Optional\PhpUnit\assertSome;
use function j45l\maybe\Optional\PhpUnit\assertSomeEquals;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Maybe\Maybe
 */
class DoTest extends TestCase
{
    public function testValueReturningCallableResultsInASuccess(): void
    {
        $success = Either::do(static function (): int {
            return 42;
        });

        assertSomeEquals(42, $success);
    }

    public function testThrowingCallableResultInAFailure(): void
    {
        $failure = Either::do(static function (): void {
            throw new RuntimeException('Runtime exception');
        });

        assertFailureReasonString('Runtime exception', $failure);
    }

    public function testNoneReturningCallableResultsSomeIsSuccess(): void
    {
        $some = Either::do(static function (): Some {
            return Some::from(42);
        });

        assertSome($some);
        assertSuccess($some);
    }

    public function testNoneReturningCallableResultsNoneIsNotASuccess(): void
    {
        $none = Either::do(static function (): None {
            return None::create();
        });

        assertNone($none);
        assertNotSuccess($none);
    }

    public function testNoneReturningCallableResultsNullIsNotASuccess(): void
    {
        $none = Either::do(static function () {
            return null;
        });

        assertNone($none);
        assertNotSuccess($none);
    }
}
