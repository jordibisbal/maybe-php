<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;

use function is_callable as isCallable;
use function is_string as isString;

/**
 * @template T
 */
trait OptionalOn
{
    /**
     * @template T2
     * @param class-string|callable(Optional<T>):bool|bool $condition
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function on($condition, $value)
    {
        switch (/** @infection-ignore-all */ true) {
            case isString($condition):
                return $this->on(is_a($this, $condition, true), $value);
            case isCallable($condition):
                return $this->on(static::do($condition, $this)->getOrElse(false), $value);
            case safe($condition)->getOrElse(false):
                return self::do($value, $this);
            default:
                return $this;
        }
    }

    /**
     * @template T2
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onSome($value)
    {
        return $this->on(Some::class, $value);
    }

    /**
     * @template T2
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onNone($value)
    {
        return $this->on(None::class, $value);
    }

    /**
     * @template T2
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onSuccess($value)
    {
        return $this->on(Success::class, $value);
    }

    /**
     * @template T2
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onJustSuccess($value)
    {
        return $this->on(JustSuccess::class, $value);
    }

    /**
     * @template T2
     * @param T2 $value
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onFailure($value)
    {
        return $this->on(Failure::class, $value);
    }
}
