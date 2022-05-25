<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Optional\Optionals;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Test\Unit\Optional\Fixtures\OptionalsFixtures;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Optional\Optionals
 */
final class OptionalsTest extends TestCase
{
    public function testCanGetSuccesses(): void
    {
        $this->assertEquals(
            [
                'justSuccess' => JustSuccess::create(),
                'some' => Some::from(42)
            ],
            OptionalsFixtures::getMixed()->successes()->items()
        );
    }

    public function testCanGetSome(): void
    {
        $this->assertEquals(
            [
                'some' => Some::from(42)
            ],
            OptionalsFixtures::getMixed()->somes()->items()
        );
    }

    public function testCanGetNone(): void
    {
        $this->assertEquals(
            [
                'none' => None::create(),
            ],
            OptionalsFixtures::getMixed()->nones()->items()
        );
    }

    public function testCanGetFailures(): void
    {
        $this->assertEquals(
            [
                'failure' => Failure::because(Reason::fromString('Failure')),
                'throwable failure' =>
                    Failure::because(
                        ThrowableReason::fromThrowable(
                            new RuntimeException('Throwable Failure')
                        )
                    ),
            ],
            OptionalsFixtures::getMixed()->failures()->items()
        );
    }

    public function testCanGetFailureReasonStrings(): void
    {
        $this->assertEquals(
            [
                'failure' => 'Failure',
                'throwable failure' => 'Throwable Failure'
            ],
            OptionalsFixtures::getMixed()->failures()->failureReasonStrings()
        );
    }

    public function testCanGetValues(): void
    {
        $this->assertEquals(
            [
                'some' => 42
            ],
            OptionalsFixtures::getMixed()->values()
        );
    }

    public function testCanEmptyAndNotEmpty(): void
    {
        $this->assertTrue(Optionals::create()->empty());
        $this->assertFalse(OptionalsFixtures::getMixed()->empty());
    }
}
