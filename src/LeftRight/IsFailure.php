<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use j45l\maybe\Either\Failure;

/**
 * @template T
 * @param LeftRight<T> $either
 */
function isFailure(LeftRight $either): bool
{
    return $either instanceof Failure;
}
