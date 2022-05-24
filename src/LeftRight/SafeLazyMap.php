<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use Closure;

use function Functional\map;

/**
 * @param mixed[] $values
 * @phpstan-return Closure():LeftRights<mixed>
 */
function safeLazyMap(array $values): Closure
{
    return function (...$parameters) use ($values) {
        return LeftRights::create(
            map(
                $values,
                function ($value) use ($parameters) {
                    return safeLazy($value)(...$parameters);
                }
            )
        );
    };
}
