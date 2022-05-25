<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Success;

if (!function_exists(__NAMESPACE__ . '\isSuccess')) {
    /**
     * @template T
     * @param Optional<T> $either
     */
    function isSuccess(Optional $either): bool
    {
        return $either instanceof Success;
    }
}
