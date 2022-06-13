<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use RuntimeException;

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

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    public function getOrRuntimeException(string $message = '')
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @phpstan-return static
     */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
