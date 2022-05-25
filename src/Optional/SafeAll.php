<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use function Functional\map;

/**
 * @param mixed[] $values
 * @phpstan-return Optionals<mixed>
 */
function safeAll(array $values): Optionals
{
    return Optionals::create(
        map(
            $values,
            function ($value) {
                return safe($value);
            }
        )
    );
}
