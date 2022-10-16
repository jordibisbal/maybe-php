<?php

namespace j45l\maybe\Test\Unit\Optional\Functions\On;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\onSome;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

/** @covers ::j45l\maybe\Optional\onSome */
class OnSomeTest extends TestCase
{
    public function testsOnSomeOnSomeExecutes(): void
    {
        $some = Some::from(42)->always(onSome($this->increaseSome(...)));

        assertEquals(43, $some->getOrFail());
    }

    public function testsOnSomeOnNoneBypasses(): void
    {
        $none = None::create();
        $result = $none->always(onSome($this->increaseSome(...)));

        assertSame($result, $none);
    }

    public function testsOnSomeOnFailureBypasses(): void
    {
        $failure = Failure::create();
        $result = $failure->always(onSome($this->increaseSome(...)));

        assertSame($result, $failure);
    }

    public function testsOnSomeOnJustSuccessBypasses(): void
    {
        $justSuccess = JustSuccess::create();
        $result = $justSuccess->always(onSome($this->increaseSome(...)));

        assertSame($result, $justSuccess);
    }

    /**
     * @param Some<int> $some
     * @return Optional<int>
     */
    private function increaseSome(Some $some): Optional
    {
        return $some->map(fn (int $value) => $value + 1);
    }
}
