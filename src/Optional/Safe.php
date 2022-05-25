<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

/**
 * @template T
 * @param T|callable():T $value
 * @phpstan-return Optional<T>
 */
function safe($value): Optional
{
    return Optional::do($value);
}
