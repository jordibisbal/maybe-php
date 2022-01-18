<?php

declare(strict_types=1);

namespace j45l\either;

class Success extends Some
{
    public static function create(): Success
    {
        return new self(true, Context::create());
    }
}
