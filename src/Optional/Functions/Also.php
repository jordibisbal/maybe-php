<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;

/**
 * @param mixed $parameters
 */
function also(callable $function, ...$parameters): Closure
{
    return static function (Optional $optional) use ($function, $parameters): Optional {
        $function($optional, ...$parameters);

        return $optional;
    };
}
