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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrElse($defaultValue)
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function takeOrElse($propertyName, $defaultValue)
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
