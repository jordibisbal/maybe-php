<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use j45l\functional\Sequences\Sequence;
use j45l\maybe\Either\Failure;

use function j45l\functional\delay;

/**
 * @template T
 * @param callable():T $callable
 * @phpstan-param  callable(Float $seconds): void $delayFn
 * @return LeftRight<T>
 */
function retry(callable $callable, int $tries, Sequence $delaySequence, $delayFn = null): LeftRight
{
    $retry = function ($maybe, Sequence $delaySequence, $triesLeft) use ($callable, $delayFn, &$retry) {
        switch (true) {
            case (!$maybe instanceof Failure) || ($triesLeft < 1):
                return $maybe;
            default:
                return $retry(
                    delay(
                        $delaySequence->value(),
                        function () use ($maybe, $callable) {
                            return $maybe->orElse($callable);
                        },
                        $delayFn
                    ),
                    $delaySequence->next(),
                    $triesLeft - 1
                );
        }
    };

    return $retry(safeLazy($callable)(), $delaySequence, $tries - 1);
}
