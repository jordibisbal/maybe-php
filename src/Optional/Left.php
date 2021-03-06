<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use RuntimeException;
use Throwable;

/**
 * @template T
 */
trait Left
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen(callable $value): Optional
    {
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    public function orElse(callable $value): Optional
    {
        return self::do($value, $this);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    public function orFail(string $message, Throwable $throwable = null): Optional
    {
        throw new RuntimeException($message, 0, $throwable);
    }
}
