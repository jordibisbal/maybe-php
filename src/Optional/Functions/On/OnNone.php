<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Maybe\None;

/**
 * @template T
 * @param Closure():Optional<T> $callback
 * @return Closure(Optional<mixed>=):(Optional<T>|None)
 */
function onNone(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof None ? $callback() : $optional;
}
