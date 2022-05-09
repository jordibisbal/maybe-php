<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\functional\Sequences\Sequence;
use j45l\maybe\Maybe;
use j45l\maybe\Some;

use function j45l\functional\delay;

/**
 * @template T
 * @param callable():T $callable
 * @phpstan-param  callable(Float $seconds): void $delayFn
 * @return Maybe<T>
 */
function retry(callable $callable, int $tries, Sequence $delaySequence, $delayFn = null): Maybe
{
    $retry = function ($maybe, Sequence $delaySequence, $triesLeft) use ($callable, $delayFn, &$retry) {
        switch (true) {
            case ($maybe instanceof Some) || ($triesLeft < 1):
                return $maybe;
            default:
                return $retry(
                    delay(
                        $delaySequence->value(),
                        function () use ($maybe, $callable) {
                            return $maybe->sink($callable);
                        },
                        $delayFn
                    ),
                    $delaySequence->next(),
                    $triesLeft - 1
                );
        }
    };

    $successOnSome = function (Maybe $maybe): Maybe {
        switch (true) {
            case $maybe instanceof Some:
                return Success::from($maybe);
            default:
                return $maybe;
        }
    };

    return $successOnSome($retry(doTry($callable), $delaySequence, $tries - 1));
}
