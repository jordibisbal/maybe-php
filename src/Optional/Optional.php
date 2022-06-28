<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\Success;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use Throwable;

use function get_class as getClass;
use function is_callable as isCallable;
use function is_null as isNull;

/**
 * @template T
 */
abstract class Optional implements Functor
{
    /** @use OptionalOn<T> */
    use OptionalOn;

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @template C
     * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C) $function
     * @param mixed $parameters
     * @return Optional<C>
     */
    public static function do(callable $function, ...$parameters): self
    {
        return self::wrap($function, ...$parameters);
    }

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @template C
     * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C)|C $value
     * @param mixed $parameters
     * @return Optional<C>|None|Some<C>
     */
    public static function wrap($value, ...$parameters): self
    {
        switch (/** @infection-ignore-all */ true) {
            case isCallable($value):
                return self::callableDo($value, ...$parameters);
            case $value instanceof self:
                return $value;
            case isNull($value):
                return None::create();
            default:
                return Some::from($value);
        }
    }

    /**
     * @param callable $value
     * @param mixed[] $params
     * @return Optional<mixed>
     */
    private static function callableDo(callable $value, ...$params): Optional
    {
        try {
            return self::wrap($value(...$params));
        } catch (Throwable $throwable) {
            return Failure::because(ThrowableReason::fromThrowable($throwable));
        }
    }

    //region (Non)Valued

    /**
     * @template R
     * @param callable(T):R $function
     * @return Optional<R>
     */
    abstract public function map(callable $function): Functor;

    /**
     * @param mixed $defaultValue
     * @return mixed
     */
    abstract public function getOrElse($defaultValue);

    /**
     * @param string $message
     * @return T
     */
    abstract public function getOrFail(string $message = '');

    /**
     * @param mixed $defaultValue
     * @param string|int|array<string|int> $propertyName
     * @return mixed
     */
    abstract public function takeOrElse($propertyName, $defaultValue);

    /**
     * @param bool|callable(Optional<T>):bool $condition
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function assert($condition, string $message = null): Optional
    {
        return Failure::because(Reason::fromString($message ?? 'failed assertion')->withSubject($this));
    }

    //endregion

    //region Optional

    /**
     * @param callable $value
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    abstract public function andThen(callable $value): Optional;

    /**
     * @param callable $value;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    abstract public function orElse(callable $value): Optional;

    /** @return Optional<T> */
    abstract public function orFail(string $message, Throwable $throwable = null): Optional;

    /** @return Either<T> */
    public function toEither(): Either
    {
        switch (/** @infection-ignore-all */ true) {
            case $this instanceof Success:
                return JustSuccess::create();
            default:
                return Failure::because(Reason::fromString(sprintf('From %s', getClass($this)))->withSubject($this));
        }
    }

    //endregion
}
