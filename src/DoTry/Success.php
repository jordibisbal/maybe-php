<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\maybe\Context\Context;
use j45l\maybe\Some;

/**
 * @template T
 * @extends Some<T>
 */
final class Success extends Some
{
    /** @return Success<T> */
    public static function create(): Success
    {
        return new self(true, Context::create());
    }

    /**
     * @param mixed $value
     * @return Success<T>
     */
    public static function from($value): Some
    {
        return new self($value, Context::create());
    }
}
