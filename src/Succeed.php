<?php

declare(strict_types=1);

namespace j45l\either;

class Succeed extends Either
{
    public static function create(): Succeed
    {
        return new self();
    }
}
