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
     * @param callable(Optional<mixed>):T2 $function
     * @phpstan-return (T2 is Optional<mixed> ? T2 : Optional<T2>)|Optional<T>
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function on($condition, callable $function)
    {
        switch (/** @infection-ignore-all */ true) {
            case isString($condition):
                return $this->on(is_a($this, $condition, true), $function);
            case isCallable($condition):
                return $this->on(static::do($condition, $this)->getOrElse(false), $function);
            case self::wrap($condition)->getOrElse(false):
                return self::do($function, $this);
            default:
                return $this;
        }
    }

    /**
     * @template T2
     * @param callable(Optional<T>):T2 $function
     * @phpstan-return (Optional<T2>|Optional<T>)
     */
    public function onSome(callable $function): Optional
    {
        return $this->on(Some::class, $function);
    }

    /**
     * @template T2
     * @param callable(Optional<T>):T2 $function
     * @phpstan-return (Optional<T2>|Optional<T>)
     */
    public function onNone(callable $function): Optional
    {
        return $this->on(None::class, $function);
    }

    /**
     * @template T2
     * @param callable(Optional<T>):T2 $function
     * @phpstan-return (Optional<T2>|Optional<T>)
     */
    public function onSuccess(callable $function): Optional
    {
        return $this->on(Success::class, $function);
    }

    /**
     * @template T2
     * @param callable(Optional<T>):T2 $function
     * @phpstan-return (Optional<T2>|Optional<T>)
     */
    public function onJustSuccess(callable $function): Optional
    {
        return $this->on(JustSuccess::class, $function);
    }

    /**
     * @template T2
     * @param callable(Optional<T>):T2 $function
     * @phpstan-return (Optional<T2>|Optional<T>)
     */
    public function onFailure(callable $function): Optional
    {
        return $this->on(Failure::class, $function);
    }
}
