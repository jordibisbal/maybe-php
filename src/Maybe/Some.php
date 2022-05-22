<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\Either\Success;
use j45l\maybe\LeftRight\Right;
use j45l\maybe\LeftRight\Valued;

/**
 * @template T
 * @extends Maybe<T>
 */
final class Some extends Maybe implements Success
{
    /** @use Valued<T> */
    use Valued;
    use Right;
}
