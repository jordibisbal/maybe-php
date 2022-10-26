<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;

/**
 * @template T
 * @phpstan-param (Closure(Optional<T>, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):Optional<T>) $function
 * @param mixed $parameters
 * @return Closure(Optional<T>): Optional<T>
 */
function also(Closure $function, ...$parameters): Closure
{
    return static function (Optional $optional) use ($function, $parameters): Optional {
        $function($optional, ...$parameters);

        return $optional;
    };
}
