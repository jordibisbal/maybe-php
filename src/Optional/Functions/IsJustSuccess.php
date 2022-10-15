<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\JustSuccess;

use function function_exists as functionExists;

if (!functionExists(__NAMESPACE__ . '\isJustSuccess')) {
    /**
     * @template T
     * @param Optional<T> $either
     */
    function isJustSuccess(Optional $either): bool
    {
        return $either instanceof JustSuccess;
    }
}
