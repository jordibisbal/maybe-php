<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

/**
 * @template C
 * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C)|C $value
 * @param mixed $parameters
 * @phpstan-return Optional<C>
 */
function safe($value, ...$parameters): Optional
{
    return Optional::do($value, ...$parameters);
}
