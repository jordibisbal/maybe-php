<?php

declare(strict_types=1);

namespace j45l\maybe\Maybe;

use j45l\maybe\LeftRight\LeftRight;

/**
 * @template T
 * @extends LeftRight<T>
 */
abstract class Maybe extends LeftRight
{
    /**
     * @param T $value
     * @return LeftRight<T>|None|Some<T>
     */
    protected static function someWrap($value): LeftRight
    {
        /** @infection-ignore-all */
        switch (true) {
            case $value instanceof LeftRight:
                return $value;
            case is_null($value):
                return None::create();
            default:
                return Some::from($value);
        }
    }
}
