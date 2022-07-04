<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\Either\Success;
use j45l\maybe\Optional\Right;
use j45l\maybe\Optional\Valued;

/**
 * @template T
 * @extends Maybe<T>
 * @implements Success<T>
 */
final class Some extends Maybe implements Success
{
    /** @use Valued<T> */
    use Valued;
    /** @use Right<T> */
    use Right;
}
