<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Failure;

if (!function_exists(__NAMESPACE__ . '\isFailure')) {
    /**
     * @template T
     * @param Optional<T> $either
     */
    function isFailure(Optional $either): bool
    {
        return $either instanceof Failure;
    }
}
