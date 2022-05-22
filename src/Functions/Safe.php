<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use Closure;
use j45l\maybe\LeftRight\LeftRight;

/**
 * @template T
 * @param T|callable():T $value
 * @phpstan-return Closure():LeftRight<T>
 */
function safe($value): Closure
{
    return function (...$parameters) use ($value) {
        return LeftRight::do($value, ...$parameters);
    };
}
