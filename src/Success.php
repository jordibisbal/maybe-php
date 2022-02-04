<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;

class Success extends Some
{
    public static function create(): Success
    {
        return new self(true, Context::create());
    }
}
