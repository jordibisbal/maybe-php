<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Maybe\Some;

use function function_exists as functionExists;

if (!functionExists(__NAMESPACE__ . '\isSome')) {
    /**
     * @template T
     * @param Optional<T> $maybe
     */
    function isSome(Optional $maybe): bool
    {
        return $maybe instanceof Some;
    }
}
