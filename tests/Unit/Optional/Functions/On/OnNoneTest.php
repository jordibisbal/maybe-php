<?php

namespace j45l\maybe\Test\Unit\Optional\Functions\On;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\onNone;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

/** @covers ::j45l\maybe\Optional\onNone */
class OnNoneTest extends TestCase
{
    public function testsOnNomeOnSomeByPasses(): void
    {
        $some = Some::from(42);
        $result = $some->always(onNone($this->changeNone(...)));

        assertSame($result, $some);
    }

    public function testsOnSomeOnNoneExecutes(): void
    {
        $none = None::create();
        $result = $none->always(onNone($this->changeNone(...)));

        self::assertInstanceOf(Some::class, $result);
        assertEquals(42, $result->get());
    }

    public function testsOnSomeOnFailureBypasses(): void
    {
        $failure = Failure::create();
        $result = $failure->always(onNone($this->changeNone(...)));

        assertSame($result, $failure);
    }

    public function testsOnSomeOnJustSuccessBypasses(): void
    {
        $justSuccess = JustSuccess::create();
        $result = $justSuccess->always(onNone($this->changeNone(...)));

        assertSame($result, $justSuccess);
    }

    /**
     * @return Optional<int>
     */
    private function changeNone(): Optional
    {
        return Some::from(42);
    }
}
