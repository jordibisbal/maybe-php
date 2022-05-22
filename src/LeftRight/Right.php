<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

trait Right
{
    /**
     * @param mixed $value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function orElse($value): LeftRight
    {
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function andThen($value): LeftRight
    {
        return $this->do($value, $this);
    }
}
