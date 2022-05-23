<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use j45l\maybe\LeftRight\LeftRight;
use j45l\maybe\Maybe\Some;

/**
 * @template T
 * @param LeftRight<T> $maybe
 */
function isSome(LeftRight $maybe): bool
{
    return $maybe instanceof Some;
}
