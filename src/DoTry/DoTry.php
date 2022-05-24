<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\maybe\Maybe;
use Throwable;

/**
 * @deprecated Move to v3
 * @template T
 * @param callable():T $callable
 * @return Maybe<T>
 */
function doTry(callable $callable): Maybe
{
    try {
        return Success::from($callable());
    } catch (Throwable $throwable) {
        return Failure::from(ThrowableReason::fromThrowable($throwable));
    }
}
