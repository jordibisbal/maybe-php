<?php

declare(strict_types=1);

namespace j45l\either;

class Success extends Either
{
    public static function create(): Success
    {
        return new self();
    }
}
