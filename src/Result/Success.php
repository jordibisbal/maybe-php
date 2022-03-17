<?php

declare(strict_types=1);

namespace j45l\either\Result;

use j45l\either\Context\Context;
use j45l\either\Some;

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
     * @param T $value
     * @return Success<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function from($value): Some
    {
        return static::create();
    }
}
