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

use function j45l\functional\value;
use function j45l\maybe\Optional\PhpUnit\assertSuccess;
use function j45l\maybe\Optional\safeAll;

/**
 * @covers \j45l\maybe\Optional\Optionals
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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

    public function testNewSomeCanBeAdded(): void
    {
        $optionals = Optionals::create([Some::from(42)]);
        $mergedOptionals = $optionals->mergeSomes(Some::from(43));

        $this->assertCount(1, $optionals);
        $this->assertCount(2, $mergedOptionals);
        $this->assertEquals($mergedOptionals->head(), Some::from(42));
        $this->assertEquals($mergedOptionals->tail()->head(), Some::from(43));
    }

    public function testNewAddingSomeOtherAreNotAdded(): void
    {
        $optionals = Optionals::create([Some::from(42)]);
        $mergedOptionals = $optionals->mergeSomes(None::create(), Failure::create(), Some::from(43));

        $this->assertCount(1, $optionals);
        $this->assertCount(2, $mergedOptionals);
        $this->assertEquals($mergedOptionals->head(), Some::from(42));
        $this->assertEquals($mergedOptionals->tail()->head(), Some::from(43));
    }

    public function testNewFailureCanBeAdded(): void
    {
        $optionals = Optionals::create([Failure::because(Reason::fromString('Failure'))]);
        $mergedOptionals = $optionals->mergeFailures(Failure::because(Reason::fromString('Another Failure')));

        $this->assertCount(1, $optionals);
        $this->assertCount(2, $mergedOptionals);
        $this->assertEquals($mergedOptionals->head(), Failure::because(Reason::fromString('Failure')));
        $this->assertEquals(
            $mergedOptionals->tail()->head(),
            Failure::because(Reason::fromString('Another Failure'))
        );
    }

    public function testNewAddingFailuresOtherAreNotAdded(): void
    {
        $optionals = Optionals::create([Failure::because(Reason::fromString('Failure'))]);
        $mergedOptionals = $optionals->mergeFailures(
            None::create(),
            Some::from(42),
            Failure::because(Reason::fromString('Another Failure'))
        );

        $this->assertCount(1, $optionals);
        $this->assertCount(2, $mergedOptionals);
        $this->assertEquals($mergedOptionals->head(), Failure::because(Reason::fromString('Failure')));
        $this->assertEquals(
            $mergedOptionals->tail()->head(),
            Failure::because(Reason::fromString('Another Failure'))
        );
    }

    public function testHeadOnEmptyOptionalsIsNone(): void
    {
        $optional = Optionals::create([]);
        $this->assertEquals(None::create(), $optional->head());
    }

    public function testHeadOnEmptyOptionalsCanBeDefaulted(): void
    {
        $optional = Optionals::create([]);
        $this->assertEquals(Some::from(42), $optional->head(Some::from(42)));
    }

    public function testHeadOnNotOptionalsDefaultIsIgnored(): void
    {
        $optional = Optionals::create([Some::from(42)]);
        $this->assertEquals(Some::from(42), $optional->head(Some::from(43)));
    }

    public function testAssertAllSucceed(): void
    {
        assertSuccess(
            safeAll([
                value(JustSuccess::create()),
                value(Some::from(42))
            ])->assertAllSucceed()
        );
    }

    public function testOnSomeFailed(): void
    {
        $all = safeAll([
            value(Failure::create()),
            value(Some::from(42))
        ]);

        $failure = $all->assertAllSucceed();

        self::assertInstanceOf(Failure::class, $failure);
        self::assertSame($all, $failure->reason()->subject()->getOrElse(null));
    }
}
