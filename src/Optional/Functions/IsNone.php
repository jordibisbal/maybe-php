<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Maybe\None;

use function function_exists as functionExists;

if (!functionExists(__NAMESPACE__ . '\isNone')) {
    /**
     * @template T
     * @param Optional<T> $maybe
     */
    function isNone(Optional $maybe): bool
    {
        return $maybe instanceof None;
    }
}
