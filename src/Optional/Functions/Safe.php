<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

/**
 * @template C
 * phpcs:ignore
 * @phpstan-param (callable():C | callable(mixed):C | callable(mixed,mixed):C | callable(mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed):C | callable(mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed,mixed):C) $function
 * @param mixed $parameters
 * @phpstan-return Optional<mixed>
 */
function safe(callable $function, ...$parameters): Optional
{
    return Optional::try($function, ...$parameters);
}
