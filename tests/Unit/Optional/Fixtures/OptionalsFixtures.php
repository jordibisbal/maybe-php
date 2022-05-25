<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Optional\Fixtures;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Optional\Optionals;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use RuntimeException;

final class OptionalsFixtures
{
    /** @return Optionals<mixed> */
    public static function getMixed(): Optionals
    {
        return Optionals::create([
            'justSuccess' => JustSuccess::create(),
            'some' => Some::from(42),
            'failure' => Failure::because(Reason::fromString('Failure')),
            'none' => None::create(),
            'throwable failure' =>
                Failure::because(ThrowableReason::fromThrowable(new RuntimeException('Throwable Failure'))),
        ]);
    }
}
