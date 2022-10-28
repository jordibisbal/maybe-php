<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Maybe\None;

/**
 * @template T2
 * @param (Closure():T2)|(Closure(None<mixed>):T2) $callback
 * @return Closure(Optional<mixed>):(T2|Optional<mixed>)
 * @noinspection PhpDocDuplicateTypeInspection
 */
function onNone(Closure $callback): Closure
{
    return static fn (Optional $optional) => $optional instanceof None ? $callback($optional) : $optional;
}
