<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

/**
 * @template C
 * @phpstan-param callable():C  $function
 * @phpstan-return Optional<C>
 */
function safe(callable $function): Optional
{
    return Optional::try($function);
}
