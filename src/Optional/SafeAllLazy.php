<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;

use function Functional\map;

/**
 * @param mixed[] $values
 * @phpstan-return Closure():Optionals<mixed>
 */
function safeAllLazy(array $values): Closure
{
    return static function (...$parameters) use ($values) {
        return Optionals::create(
            map(
                $values,
                function ($value) use ($parameters) {
                    return safeLazy($value)(...$parameters);
                }
            )
        );
    };
}
