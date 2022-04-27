<?php

declare(strict_types=1);

namespace j45l\maybe;

/**
 * @template T
 * @param T|Maybe<T> $value
 * @phpstan-return Some<T>|None<T>
 */
function safe($value): Maybe
{
    switch (true) {
        case is_callable($value):
            return safe(Deferred::create($value)->resolve());
        case !$value instanceof Maybe:
            return safe(Some::from($value));
        case $value instanceof Deferred:
            return safe($value->resolve());
        case $value instanceof Some && !is_null($value->get()):
            return $value;
        default:
            return None::create();
    }
}
