<?php

declare(strict_types=1);

namespace j45l\maybe;

/**
 * @template T
 * @param Maybe<T> $value
 * @phpstan-return Some<T>|None<T>
 */
function someOrNone(Maybe $value): Maybe
{
    switch (true) {
        case $value instanceof Deferred:
            return someOrNone($value->resolve());
        case $value instanceof Some && !is_null($value->get()):
            return $value;
        default:
            return None::create();
    }
}
