<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;

class None extends Either
{
    public static function create(): self
    {
        return new self();
    }

    public function orElse($defaultValue): Either
    {
        return self::build($defaultValue, $this->context());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function then($nextValue): Either
    {
        return self::build($this, $this->context());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function pipe(Closure $closure): Either
    {
        return $this;
    }
}
