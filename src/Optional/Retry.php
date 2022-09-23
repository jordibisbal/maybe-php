<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Sequences\Sequence;
use j45l\maybe\Either\Failure;

use function Functional\partial_right;
use function j45l\functional\delay;

/**
 * @template T
 * @param callable():T $callable
 * @phpstan-param  callable(Float $seconds): void $delayFn
 * @return Optional<T>
 */
function retry(callable $callable, int $tries, Sequence $delaySequence, $delayFn = null): Optional
{
    $retry = static function ($maybe, Sequence $delaySequence, $triesLeft) use ($callable, $delayFn, &$retry, $tries) {
        switch (/** @infection-ignore-all */ true) {
            case (!$maybe instanceof Failure) || ($triesLeft < 1):
                return $maybe;
            default:
                return $retry(
                    delay(
                        $delaySequence->value(),
                        function () use ($maybe, $callable, $tries, $triesLeft) {
                            /** @var int $triesLeft */
                            return $maybe->orElse(
                                partial_right($callable, [$tries - $triesLeft, $triesLeft === 0])
                            );
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
