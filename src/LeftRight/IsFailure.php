<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use j45l\maybe\Either\Failure;
use j45l\maybe\LeftRight\LeftRight;

/**
 * @template T
 * @param LeftRight<T> $either
 */
function isFailure(LeftRight $either): bool
{
    return $either instanceof Failure;
}
