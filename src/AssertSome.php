<?php
declare(strict_types=1);

namespace j45l\maybe;

use Exception;
use LogicException;

/**
 * @template T
 * @param Maybe<T> $maybe
 * @return Some<T>
 * @throws Exception
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
