<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Throwable;

/** @template T */
trait Right
{
    /**
     * @param mixed $value
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function orElse($value): Optional
    {
        return $this;
    }

    /**
     * @param mixed $value
     * @return Optional<T>
     */
    public function andThen($value): Optional
    {
        return self::do($value, $this);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    public function orFail(string $message, Throwable $throwable = null): Optional
    {
        return $this;
    }
}
