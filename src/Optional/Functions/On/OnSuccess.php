<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Either\Success;

/**
 * @template T
 * @template T2
 * @param Closure(Success<T>):Optional<T2> $callback
 * @return Closure(Optional<T>=):Optional<T|T2>
 */
function onSuccess(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof Success ? $callback($optional) : $optional;
}
