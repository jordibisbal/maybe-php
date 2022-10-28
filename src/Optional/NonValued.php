<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use RuntimeException;
use Throwable;

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

    /** @throws Throwable */
    public function getOrFail(RuntimeException|string $message = null): void
    {
        throw $this->runtimeException($message);
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map(callable $function): static
    {
        return $this;
    }

    private function runtimeException(string|RuntimeException|null $message): RuntimeException
    {
        return match (/** @infection-ignore-all */ true) {
            $message instanceof RuntimeException => $message,
            default => new RuntimeException($message ?? '')
        };
    }
}
