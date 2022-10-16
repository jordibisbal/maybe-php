<?php

namespace j45l\maybe\Test\Unit\Optional\Functions\On;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\onFailure;
use function j45l\maybe\Optional\reason;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

/** @covers ::j45l\maybe\Optional\onFailure */
class OnFailureTest extends TestCase
{
    public function testsOnNomeOnSomeByPasses(): void
    {
        $some = Some::from(42);
        $result = $some->always(onFailure($this->changeFailure(...)));

        assertSame($result, $some);
    }

    public function testsOnSomeOnNoneBypasses(): void
    {
        $none = None::create();
        $result = $none->always(onFailure($this->changeFailure(...)));

        assertSame($result, $none);
    }

    public function testsOnSomeOnFailureBypasses(): void
    {
        $none = Failure::because(FailureReason::fromString('4'));
        $result = $none->always(onFailure($this->changeFailure(...)));

        self::assertInstanceOf(Failure::class, $result);
        assertEquals('42', reason($result)->toString());
    }

    public function testsOnSomeOnJustSuccessBypasses(): void
    {
        $none = JustSuccess::create();
        $result = $none->always(onFailure($this->changeFailure(...)));

        assertSame($result, $none);
    }

    /**
     * @template T
     * @param Failure<T> $optional
     * @return Failure<T>
     */
    private function changeFailure(Failure $optional): Failure
    {
        return Failure::because(FailureReason::fromString($optional->reason()->toString() . '2'));
    }
}
