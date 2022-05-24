<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use j45l\maybe\Either\Success;

/**
 * @template T
 * @param LeftRight<T> $either
 */
function isSuccess(LeftRight $either): bool
{
    return $either instanceof Success;
}
