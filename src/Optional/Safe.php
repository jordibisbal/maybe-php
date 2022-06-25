<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;

/**
 * @template T
 * @param (Closure(mixed...):T)|(Closure(mixed):T)|(Closure(mixed, mixed):T)|(Closure(mixed, mixed, mixed):T)|T $value
 * @param mixed $parameters
 * @phpstan-return Optional<T>
 */
function safe($value, ...$parameters): Optional
{
    return Optional::do($value, ...$parameters);
}
