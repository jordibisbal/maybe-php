<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Failure;

use function function_exists as functionExists;

if (!functionExists(__NAMESPACE__ . '\isFailure')) {
    /**
     * @template T
     * @param Optional<T> $either
     */
    function isFailure(Optional $either): bool
    {
        return $either instanceof Failure;
    }
}
