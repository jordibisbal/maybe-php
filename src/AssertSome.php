<?php

declare(strict_types=1);

namespace j45l\maybe;

use Exception;
use LogicException;

/**
 * @deprecated Move to v3
 * @template T
 * @template ET of \Exception
 * @param Maybe<T> $maybe
 * @param ET $exception
 * @return Some<T>
 * @throws ET | LogicException
 */
function assertSome(Maybe $maybe, Exception $exception = null): Some
{
    switch (true) {
        case $maybe instanceof Some:
            return $maybe;
        default:
            throw $exception ?? new LogicException('Unable to get value');
    }
}
