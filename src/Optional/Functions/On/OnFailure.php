<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Either\Failure;

/**
 * @template T
 * @template T2
 * @param Closure(Failure<T>):Optional<T2> $callback
 * @return Closure(Optional<T>=:Optional<T|T2>
 */
function onFailure(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof Failure ? $callback($optional) : $optional;
}
