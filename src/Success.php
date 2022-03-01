<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;

/**
 * @template T
 * @extends Some<T>
 */
class Success extends Some
{
    /** @return Success<T> */
    public static function create(): Success
    {
        return new self(true, Context::create());
    }

    /**
     * @param T $value
     * @return Success<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function from($value): Some
    {
        return static::create();
    }
}
