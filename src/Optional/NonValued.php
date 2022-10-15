<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use RuntimeException;

/**
 * @template T
 */
trait NonValued
{
    private function __construct()
    {
    }

    /**
     * @template D
     * @return D
     */
    public function getOrElse(mixed $defaultValue): mixed
    {
        return $defaultValue;
    }

    /**
     * @return T
     */
    public function getOrFail(string $message = '')
    {
        throw new RuntimeException($message);
    }

    /**
     * @template D
     * @param D $defaultValue
     * @param string|int|array<string|int> $propertyName
     * @return mixed|D
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function takeOrElse(string|int|array $propertyName, mixed $defaultValue): mixed
    {
        return $defaultValue;
    }

    /**
     * @template R
     * @param callable(T):R $function
     * @return Optional<R>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
