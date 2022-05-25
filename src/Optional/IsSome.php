<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Maybe\Some;

if (!function_exists(__NAMESPACE__ . '\isSome')) {
    /**
     * @template T
     * @param Optional<T> $maybe
     */
    function isSome(Optional $maybe): bool
    {
        return $maybe instanceof Some;
    }
}
