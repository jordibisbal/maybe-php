<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use Closure;

use function Functional\map;

/**
 * @param mixed[] $values
 * @phpstan-return Closure():iterable<LeftRight<mixed>>
 */
function safeMap(array $values): Closure
{
    return function (...$parameters) use ($values) {
        return map(
            $values,
            function ($value) use ($parameters) {
                return safe($value, ...$parameters)();
            }
        );
    };
}
