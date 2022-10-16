<?php

namespace j45l\maybe\Test\Unit\Optional\Functions\On;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use j45l\maybe\Optional\Optional;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\onJustSuccess;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

/** @covers ::j45l\maybe\Optional\onJustSuccess */
class OnJustSuccessTest extends TestCase
{
    public function testsOnNomeOnSomeByPasses(): void
    {
        $some = Some::from(42);
        $result = $some->always(onJustSuccess($this->changeJustSuccess(...)));

        assertSame($result, $some);
    }

    public function testsOnSomeOnNoneBypasses(): void
    {
        $none = None::create();
        $result = $none->always(onJustSuccess($this->changeJustSuccess(...)));

        assertSame($result, $none);
    }

    public function testsOnSomeOnFailureExecutes(): void
    {
        $none = Failure::create();
        $result = $none->always(onJustSuccess($this->changeJustSuccess(...)));

        assertSame($result, $none);
    }

    public function testsOnSomeOnJustSuccessBypasses(): void
    {
        $none = JustSuccess::create();
        $result = $none->always(onJustSuccess($this->changeJustSuccess(...)));

        self::assertInstanceOf(Some::class, $result);
        assertEquals(42, $result->get());
    }

    /**
     * @return Optional<int>
     */
    private function changeJustSuccess(): Optional
    {
        return Some::from(42);
    }
}
