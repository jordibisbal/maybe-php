<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use function Functional\map;

/**
 * @param callable[] $values
 * @phpstan-return Optionals<mixed>
 */
function safeAll(array $values): Optionals
{
    return Optionals::create(
        map(
            $values,
            function (callable $value) {
                return safe($value);
            }
        )
    );
}
