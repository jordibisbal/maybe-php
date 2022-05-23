<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\None;

/**
 * @template T
 * @param LeftRight<T> $maybe
 */
function isNone(LeftRight $maybe): bool
{
    return $maybe instanceof None;
}
