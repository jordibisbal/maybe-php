<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Either\Success;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use RuntimeException;
use Throwable;

use function get_class as getClass;
use function is_callable as isCallable;
use function is_null as isNull;

/**
 * @template T
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Optional implements Functor
{
    /**
     * @template C
     * @phpstan-param C|(callable():C) $value
     * @return Optional<C>|None|Some<C>
     */
    public static function try(mixed $value): self|None|Some
    {
        return match (/** @infection-ignore-all */ true) {
            isCallable($value) => self::tryTo($value),
            $value instanceof self => $value,
            isNull($value) => None::create(),
            default => Some::from($value)
        };
    }

    /**
     * @template C
     * @phpstan-param callable():C $function
     * @return Optional<C>
     */
    private static function tryTo(callable $function): Optional
    {
        try {
            return self::try($function());
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
    abstract public function map(callable $function): Optional;

    /**
     * @template D
     * @param D $defaultValue
     * @return T|D
     */
    abstract public function getOrElse(mixed $defaultValue): mixed;

    /** @return T */
    abstract public function getOrFail(RuntimeException|string $message = null);

    /**
     * @template D
     * @param D $defaultValue
     * @param string|int|array<string|int> $propertyName
     * @return mixed|D
     */
    abstract public function takeOrElse(string|int|array $propertyName, mixed $defaultValue): mixed;

    //endregion

    //region Optional

    /**
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen(callable $value): Optional
    {
        return $this;
    }

    /**
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function bind(callable $function): Optional
    {
        return $this;
    }

    /**
     * @param callable $value;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    abstract public function orElse(callable $value): Optional;

    /** @return Optional<T> */
    abstract public function orFail(string $message, Throwable $throwable = null): Optional;

    /**
     * @template C
     * @phpstan-param (callable():C | callable($this): C) $function
     * @return Optional<C>
     */
    public function always(callable $function): Optional
    {
        return self::try(fn () => $function($this));
    }

    /** @return Either<T> */
    public function toEither(): Either
    {
        return match (/** @infection-ignore-all */ true) {
            $this instanceof Success => JustSuccess::create(),
            default => Failure::because(
                FailureReason::fromString(sprintf('From %s', getClass($this)))->withSubject($this)
            )
        };
    }

    //endregion
}
