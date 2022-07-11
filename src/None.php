<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\functional\Functor;

/**
 * @deprecated Move to v3
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
     * @param array<mixed> $parameters
     * @return Maybe<T>
     */
    public function orElse($value, ...$parameters): Maybe
    {
        return $this->next($value, ...$parameters);
    }

    /**
     * @param T $value
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value, ...$parameters): Maybe
    {
        return $this;
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
