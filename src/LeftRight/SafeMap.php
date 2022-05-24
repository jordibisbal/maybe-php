<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use function Functional\map;

/**
 * @param mixed[] $values
 * @phpstan-return LeftRights<mixed>
 */
function safeMap(array $values): LeftRights
{
    return LeftRights::create(
        map(
            $values,
            function ($value) {
                return safe($value);
            }
        )
    );
}
