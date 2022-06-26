<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\Optional\Optional;
use function is_null as isNull;

/**
 * @template T
 * @extends Optional<T>
 */
abstract class Maybe extends Optional
{
    /**
     * @param T $value
     * @return Optional<T>|None|Some<T>
     */
    protected static function someWrap($value): Optional
    {
        switch (/** @infection-ignore-all */  true) {
            case $value instanceof Optional:
                return $value;
            case isNull($value):
                return None::create();
            default:
                return Some::from($value);
        }
    }
}
