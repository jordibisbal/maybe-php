<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\LeftRight;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Test\Unit\LeftRight\Fixtures\LeftRightsFixtures;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\LeftRight\LeftRights
 */
final class LeftRightsTest extends TestCase
{
    public function testCanGetSuccesses(): void
    {
        $this->assertEquals(
            [
                'justSuccess' => JustSuccess::create(),
                'some' => Some::from(42)
            ],
            LeftRightsFixtures::getMixed()->successes()->items()
        );
    }

    public function testCanGetSome(): void
    {
        $this->assertEquals(
            [
                'some' => Some::from(42)
            ],
            LeftRightsFixtures::getMixed()->somes()->items()
        );
    }

    public function testCanGetNone(): void
    {
        $this->assertEquals(
            [
                'none' => None::create(),
            ],
            LeftRightsFixtures::getMixed()->nones()->items()
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
            LeftRightsFixtures::getMixed()->failures()->items()
        );
    }

    public function testCanGetFailureReasonStrings(): void
    {
        $this->assertEquals(
            [
                'failure' => 'Failure',
                'throwable failure' => 'Throwable Failure'
            ],
            LeftRightsFixtures::getMixed()->failures()->failureReasonStrings()
        );
    }

    public function testCanGetValues(): void
    {
        $this->assertEquals(
            [
                'some' => 42
            ],
            LeftRightsFixtures::getMixed()->values()
        );
    }
}
