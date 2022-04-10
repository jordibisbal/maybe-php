<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\maybe\Deferred;
use j45l\maybe\Maybe;
use j45l\maybe\Some;

/**
 * @template T
 * @param callable():T $callable
 * @return Maybe<T>
 */
function doTry(callable $callable): Maybe
{
    $wrap = function (Maybe $value): Maybe {
        switch (true) {
            case $value instanceof Some:
                return Success::from($value->get());
            default:
                return $value;
        }
    };

    return $wrap(Deferred::create($callable)->resolve());
}
