<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \j45l\maybe\Optional\Optional
 * @covers \j45l\maybe\Optional\OptionalOn
 * @covers \j45l\maybe\Maybe\None
 * @covers \j45l\maybe\Maybe\Some
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class OptionalOnTest extends TestCase
{
    public function testMatchingClassReturnsValue(): void
    {
        $maybe = Some::from(1)
            ->on(
                Some::class,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testTrueReturnsValue(): void
    {
        $maybe = Some::from(1)
            ->on(
                true,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testTrulyReturnsValue(): void
    {
        $maybe = Some::from(1)
            ->on(
                1, // @phpstan-ignore-line
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testNullReturnsCurrent(): void
    {
        $maybe = Some::from(1)
            ->on(
                null, // @phpstan-ignore-line
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testTrueCallableReturnsValue(): void
    {
        $maybe = Some::from(1)
            ->on(
                function () {
                    return true;
                },
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testMatchingClassCurrentIsPassed(): void
    {
        $maybe = Some::from(41)
            ->on(
                Some::class,
                function (Some $some) {
                    return Some::from($some->get() + 1);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testNotMatchingClassBypasses(): void
    {
        $maybe = Some::from(1)
            ->on(
                None::class,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testFalseBypasses(): void
    {
        $maybe = Some::from(1)
            ->on(
                false,
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testFalseCallableBypasses(): void
    {
        $maybe = Some::from(1)
            ->on(
                function () {
                    return false;
                },
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testFalseCallableOnOptionalBypasses(): void
    {
        $maybe = Some::from(false)
            ->on(
                function ($optional): bool {
                    return $optional->getOrElse(false);
                },
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(false, $maybe->get());
    }

    public function testTrueCallableOnOptionalEvaluates(): void
    {
        $maybe = Some::from(1)
            ->on(
                function ($optional) {
                    return $optional;
                },
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(42, $maybe->get());
    }

    public function testFailingCallableBypasses(): void
    {
        $maybe = Some::from(1)
            ->on(
                function () {
                    throw new RuntimeException();
                },
                function () {
                    return Some::from(42);
                }
            );

        self::assertInstanceOf(Some::class, $maybe);
        self::assertEquals(1, $maybe->get());
    }

    public function testOnSomeAlias(): void
    {
        $maybe = Some::from('notMatched');

        $onSome = $maybe->onSome('Matched');
        $onNone = $maybe->onNone('Matched');
        $onSuccess = $maybe->onSuccess('Matched');
        $onJustSuccess = $maybe->onJustSuccess('Matched');
        $onFailure = $maybe->onFailure('Matched');

        self::assertEquals('Matched', $onSome->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onNone->getOrElse('notMatched'));
        self::assertEquals('Matched', $onSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onJustSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onFailure->getOrElse('notMatched'));
    }

    public function testOnNoneAlias(): void
    {
        $maybe = None::create();

        $onSome = $maybe->onSome('Matched');
        $onNone = $maybe->onNone('Matched');
        $onSuccess = $maybe->onSuccess('Matched');
        $onJustSuccess = $maybe->onJustSuccess('Matched');
        $onFailure = $maybe->onFailure('Matched');

        self::assertEquals('notMatched', $onSome->getOrElse('notMatched'));
        self::assertEquals('Matched', $onNone->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onJustSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onFailure->getOrElse('notMatched'));
    }

    public function testOnJustSuccessAlias(): void
    {
        $maybe = JustSuccess::create();

        $onSome = $maybe->onSome('Matched');
        $onNone = $maybe->onNone('Matched');
        $onSuccess = $maybe->onSuccess('Matched');
        $onJustSuccess = $maybe->onJustSuccess('Matched');
        $onFailure = $maybe->onFailure('Matched');

        self::assertEquals('notMatched', $onSome->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onNone->getOrElse('notMatched'));
        self::assertEquals('Matched', $onSuccess->getOrElse('notMatched'));
        self::assertEquals('Matched', $onJustSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onFailure->getOrElse('notMatched'));
    }

    public function testOnFailureAlias(): void
    {
        $maybe = Failure::create();

        $onSome = $maybe->onSome('Matched');
        $onNone = $maybe->onNone('Matched');
        $onSuccess = $maybe->onSuccess('Matched');
        $onJustSuccess = $maybe->onJustSuccess('Matched');
        $onFailure = $maybe->onFailure('Matched');

        self::assertEquals('notMatched', $onSome->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onNone->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onSuccess->getOrElse('notMatched'));
        self::assertEquals('notMatched', $onJustSuccess->getOrElse('notMatched'));
        self::assertEquals('Matched', $onFailure->getOrElse('notMatched'));
    }
}
