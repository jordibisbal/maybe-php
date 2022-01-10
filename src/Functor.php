<?php

namespace j45l\either;

use Closure;

interface Functor
{
    public function map(Closure $closure): Functor;
}

