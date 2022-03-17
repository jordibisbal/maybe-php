<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\functional\Functor;

/**
 * @template T
 * @extends Either<T>
 */
class None extends Either
{
    /** @return None<T> */
    public static function create(): None
    {
        return new self();
    }

    /**
     * @param T $value
     * @return Either<T>
     */
    public function orElse($value): Either
    {
        return self::build($value, $this->context());
    }

    /**
     * @param T $value
     * @return Either<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value): Either
    {
        return self::build($this, $this->context());
    }

    /**
     * @return Either<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function pipe(callable $callable): Either
    {
        return $this;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function map(callable $callable): Functor
    {
        return $this;
    }
}
