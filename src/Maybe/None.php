<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\LeftRight\Left;
use j45l\maybe\LeftRight\NonValued;

/**
 * @extends Maybe<mixed>
 */
final class None extends Maybe
{
    use NonValued;
    /** @use Left<mixed> */
    use Left;

    public static function create(): None
    {
        return new self();
    }
}
