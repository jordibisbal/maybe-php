<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\Optional\Optional;

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
            case is_null($value):
                return None::create();
            default:
                return Some::from($value);
        }
    }
}
