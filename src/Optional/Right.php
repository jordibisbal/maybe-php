<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

trait Right
{
    /**
     * @param mixed $value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function orElse($value): Optional
    {
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function andThen($value): Optional
    {
        return $this->do($value, $this);
    }
}
