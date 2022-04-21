<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\functional\Functor;

/**
 * @template T
 * @extends Maybe<T>
 */
class None extends Maybe
{
    /** @return None<T> */
    public static function create(): None
    {
        return new self();
    }

    /**
     * @param T $value
     * @return Maybe<T>
     */
    public function orElse($value): Maybe
    {
        return self::build($value, $this->context());
    }

    /**
     * @param T $value
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value): Maybe
    {
        return self::build($this, $this->context());
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
