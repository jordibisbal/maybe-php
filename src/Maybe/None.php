<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\Optional\Left;
use j45l\maybe\Optional\NonValued;

/**
 * @template T
 * @extends Maybe<T>
 */
final class None extends Maybe
{
    /** @use NonValued<T> */
    use NonValued;
    /** @use Left<T> */
    use Left;

    /** @return None<T> */
    public static function create(): None
    {
        return new self();
    }
}
