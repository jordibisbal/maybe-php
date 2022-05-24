<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

/**
 * @template T
 */
trait Left
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value): LeftRight
    {
        return $this;
    }

    /**
     * @param mixed $value;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return LeftRight<T>
     */
    public function orElse($value): LeftRight
    {
        return $this->do($value, $this);
    }
}
