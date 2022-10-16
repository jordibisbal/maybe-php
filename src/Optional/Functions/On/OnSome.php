<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Maybe\Some;

/**
 * @template T
 * @template T2
 * @param Closure(Some<T>):Optional<T2> $callback
 * @return Closure(Optional<T>=):Optional<T|T2>
 */
function onSome(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof Some ? $callback($optional) : $optional;
}
