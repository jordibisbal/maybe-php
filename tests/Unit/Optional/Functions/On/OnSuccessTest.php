<?php

namespace j45l\maybe\Test\Unit\Optional\Functions\On;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\onSuccess;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

/** @covers ::j45l\maybe\Optional\onSuccess */
class OnSuccessTest extends TestCase
{
    public function testsOnSomeOnSomeExecutes(): void
    {
        $some = Some::from(42)->always(onSuccess($this->increaseSomeOrFortyTwo(...)));

        self::assertInstanceOf(Some::class, $some);
        assertEquals(43, $some->getOrFail());
    }

    public function testsOnSomeOnNoneBypasses(): void
    {
        $none = None::create();
        $result = $none->always(onSuccess($this->increaseSomeOrFortyTwo(...)));

        assertSame($result, $none);
    }

    public function testsOnSomeOnFailureBypasses(): void
    {
        $failure = Failure::create();
        $result = $failure->always(onSuccess($this->increaseSomeOrFortyTwo(...)));

        assertSame($result, $failure);
    }

    public function testsOnSomeOnJustSuccessExecutes(): void
    {
        $justSuccess = JustSuccess::create();
        $result = $justSuccess->always(onSuccess($this->increaseSomeOrFortyTwo(...)));

        self::assertInstanceOf(Some::class, $result);
        assertEquals(42, $result->getOrFail());
    }

    /**
     * @param Success<int> $success
     * @return Optional<int>
     */
    private function increaseSomeOrFortyTwo(Success $success): Optional
    {
        return match (true) {
            $success instanceof Some => $success->map(fn (int $value) => $value + 1),
            default => Some::from(42)
        };
    }
}
