<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional\Functions;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\isFailure;
use function j45l\maybe\Optional\isJustSuccess;
use function j45l\maybe\Optional\isNone;
use function j45l\maybe\Optional\isSome;
use function j45l\maybe\Optional\isSuccess;

/**
 * @covers \j45l\maybe\Optional\isFailure
 * @covers \j45l\maybe\Optional\isNone
 * @covers \j45l\maybe\Optional\isSome
 * @covers \j45l\maybe\Optional\isSuccess
 * @covers \j45l\maybe\Optional\isJustSuccess
 */
final class IsFunctionsTest extends TestCase
{
    public function testIsFailure(): void
    {
        $test = static function (Optional $optional) {
            return isFailure($optional);
        };

        $this->assertTrue($test(Failure::create()));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertFalse($test(None::create()));
        $this->assertFalse($test(Some::from(null)));
        $this->assertFalse($test(Some::from(42)));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertFalse($test(Some::from(42)));
    }

    public function testIsJustSuccess(): void
    {
        $test = static function (Optional $optional) {
            return isJustSuccess($optional);
        };

        $this->assertFalse($test(Failure::create()));
        $this->assertTrue($test(JustSuccess::create()));
        $this->assertFalse($test(None::create()));
        $this->assertFalse($test(Some::from(null)));
        $this->assertFalse($test(Some::from(42)));
        $this->assertTrue($test(JustSuccess::create()));
        $this->assertFalse($test(Some::from(42)));
    }

    public function testIsNone(): void
    {
        $test = static function (Optional $optional) {
            return isNone($optional);
        };

        $this->assertFalse($test(Failure::create()));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertTrue($test(None::create()));
        $this->assertFalse($test(Some::from(null)));
        $this->assertFalse($test(Some::from(42)));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertFalse($test(Some::from(42)));
    }

    public function testIsSome(): void
    {
        $test = static function (Optional $optional) {
            return isSome($optional);
        };

        $this->assertFalse($test(Failure::create()));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertFalse($test(None::create()));
        $this->assertTrue($test(Some::from(null)));
        $this->assertTrue($test(Some::from(42)));
        $this->assertFalse($test(JustSuccess::create()));
        $this->assertTrue($test(Some::from(42)));
    }

    public function testIsSuccess(): void
    {
        $test = static function (Optional $optional) {
            return isSuccess($optional);
        };

        $this->assertFalse($test(Failure::create()));
        $this->assertTrue($test(JustSuccess::create()));
        $this->assertFalse($test(None::create()));
        $this->assertTrue($test(Some::from(null)));
        $this->assertTrue($test(Some::from(42)));
        $this->assertTrue($test(JustSuccess::create()));
        $this->assertTrue($test(Some::from(42)));
    }
}
