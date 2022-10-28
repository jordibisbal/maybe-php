<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use RuntimeException;

use function j45l\functional\take;

/**
 * @template T
 */
trait Valued
{
    /** @var T */
    private mixed $value;

    /** @param T $value */
    private function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * @param T $value
     * @return self<T>
     * @noinspection PhpDocSignatureInspection
     */
    public static function from(mixed $value): self
    {
        return new self($value);
    }

    /** @return T */
    public function get()
    {
        return $this->value;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return mixed
     */
    public function getOrElse($defaultValue): mixed
    {
        return $this->get();
    }

    /**
     * @return T
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrFail(RuntimeException|string $message = null)
    {
        return $this->get();
    }

    /**
     * @template D
     * @param D $defaultValue
     * @param string|int|array<string|int> $propertyName
     * @return mixed|D
     */
    public function takeOrElse($propertyName, $defaultValue): mixed
    {
        return take($this->get(), $propertyName, $defaultValue);
    }

    /**
     * @template R
     * @param callable(T):R $function
     * @return Optional<R>
     */
    public function bind(callable $function): Optional
    {
        /** @phpstan-ignore-next-line  */
        return static::try(fn () => $function($this->get()));
    }
}
