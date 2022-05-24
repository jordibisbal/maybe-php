<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

/**
 * @template T
 * @param T|callable():T $value
 * @phpstan-return LeftRight<T>
 */
function safe($value): LeftRight
{
    return LeftRight::do($value);
}
