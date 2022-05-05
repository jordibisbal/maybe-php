<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\functional\Sequences\LinearSequence;
use j45l\functional\Sequences\Sequence;
use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
use j45l\maybe\Some;

use function j45l\functional\delay;

/**
 * @template T
 * @param callable():T $callable
 * @phpstan-param  callable(Float $seconds): void $delayFn
 * @return Maybe<T>
 */
function doTry(callable $callable, int $tries = 1, Sequence $delaySequence = null, $delayFn = null): Maybe
{
    $successWrap = function (Maybe $value): Maybe {
        switch (true) {
            case $value instanceof Some:
                return Success::from($value->get());
            default:
                return $value;
        }
    };

    $retry = function (Maybe $maybe) use ($callable, $delaySequence, $delayFn, $tries) {
        switch (true) {
            case $tries <= 1:
                return $maybe;
            default:
                return delay(
                    ($delaySequence ?? LinearSequence::create(1, 1))->value(),
                    function () use ($maybe, $callable, $delaySequence, $delayFn, $tries) {
                        return $maybe->next(doTry(
                            $callable,
                            $tries - 1,
                            ($delaySequence ?? LinearSequence::create(1, 1))->next(),
                            $delayFn
                        ));
                    },
                    $delayFn
                );
        }
    };

    return $successWrap(Deferred::create($callable)->resolve()->sink($retry));
}
