<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\Trail;
use j45l\maybe\DoTry\ThrowableReason;
use j45l\maybe\None;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Reason;
use j45l\maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\functional\pluck;
use function j45l\functional\unindex;
use function j45l\maybe\DoTry\doTry;

/**
 * @covers \j45l\maybe\Context\Trail
 * @covers \j45l\maybe\Context\MaybeAware
 */
final class TrailTest extends TestCase
{
    public function testPushingToATrailDoesNotModifyIt(): void
    {
        $trail = Trail::create();
        $secondTrail = $trail->push(None::create());

        self::assertCount(0, $trail->asArray());
        self::assertCount(1, $secondTrail->asArray());
    }

    public function testGettingAllDoesNotReturnNoneMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Some::from(2))
        ;

        self::assertEquals([1, 2], unindex($trail->someValues()));
    }

    public function testGettingButLastDoesNotReturnLastMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->someValues());
    }

    public function testGettingLastReturnsLastOne(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([Some::from(3)], $trail->last()->asArray());
    }

    public function testGettingLastFromEmptyTrailReturnsEmptyTrail(): void
    {
        self::assertTrue(Trail::create()->last()->empty());
    }

    public function testButLastDoesNotModifyTrail(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(Some::from(2))
            ->push(Some::from(3))
        ;

        self::assertEquals([1, 2], $trail->butLast()->someValues());
        self::assertEquals([1, 2, 3], $trail->someValues());
    }

    public function testGettingFailureDoesNotReturnOtherMaybe(): void
    {
        $trail = (Trail::create())
            ->push(Some::from(1))
            ->push(None::create())
            ->push(Failure::from(Reason::fromString('failed')))
        ;

        self::assertCount(1, $trail->failed());
        self::assertEquals('failed', $trail->failed()[0]->reason()->toString());
    }

    public function testThrowableFailureReasonsCanBrRetrieved(): void
    {
        $failure = doTry(function () {
            throw new RuntimeException('Exception reason');
        })->orElse(doTry(function () {
            throw new RuntimeException('Another exception reason');
        }));

        $this->assertInstanceOf(Failure::class, $failure);
        $reason = $failure->reason();
        $this->assertInstanceOf(ThrowableReason::class, $reason);
        $this->assertInstanceOf(RuntimeException::class, $reason->throwable());
        $this->assertEquals(
            ['Exception reason', 'Another exception reason'],
            pluck($failure->trail()->failureReasons(), ['throwable', 'getMessage'])
        );
        $this->assertEquals(
            ['Exception reason', 'Another exception reason'],
            $failure->trail()->failureReasonStrings()
        );
    }

    public function testResolvingASomeDoesNotAddsToTheTrail(): void
    {
        $some = Some::from(42)->resolve()->resolve()->resolve();

        self::assertCount(1, $some->trail());
    }

    public function testCanBeCheckForEmptiness(): void
    {
        self::assertTrue(Trail::create()->empty());
    }
}
