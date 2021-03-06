<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\Reason;

use function is_callable as isCallable;
use function j45l\functional\take;
use function j45l\maybe\Optional\safe as safeWrap;

/**
 * @template T
 */
trait Valued
{
    /** @var mixed */
    private $value;

    /** @param T $value */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param T $value
     * @return static<T>
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function from($value)
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
    public function getOrElse($defaultValue)
    {
        return $this->get();
    }

    /**
     * @return T
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrFail(string $message = '')
    {
        return $this->get();
    }

    public function takeOrElse($propertyName, $defaultValue)
    {
        return take($this->get(), $propertyName, $defaultValue);
    }

    /**
     * @template R
     * @param callable(T):R $function
     * @return Optional<R>
     */
    public function map(callable $function): Functor
    {
        return static::do($function, $this->get());
    }

    /**
     * @param bool|callable(mixed):bool $condition
     * @return Optional<T>
     */
    public function assert($condition, string $message = null): Optional
    {
        switch (/** @infection-ignore-all */ true) {
            case isCallable($condition):
                return $this->assert(
                    safeWrap(function () use ($condition) {
                        return $condition($this);
                    })->getOrElse(false),
                    $message
                );
            default:
                return $condition
                    ? $this :
                    Failure::because(Reason::fromString($message ?? 'failed assertion')->withSubject($this));
        }
    }
}
