<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Either\Failure;

/**
 * @template T2
 * @param (Closure():T2)|(Closure(Failure<mixed>):T2) $callback
 * @return Closure(Optional<mixed>):(T2|Optional<mixed>)
 * @noinspection PhpDocDuplicateTypeInspection
 */
function onFailure(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof Failure ? $callback($optional) : $optional;
}
