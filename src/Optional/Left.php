<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

/**
 * @template T
 */
trait Left
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value): Optional
    {
        return $this;
    }

    /**
     * @param mixed $value;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    public function orElse($value): Optional
    {
        return $this->do($value, $this);
    }
}
