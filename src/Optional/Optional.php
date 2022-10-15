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
     * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C) $function
     * @param mixed $parameters
     * @return Optional<C>
     */
    public static function try(callable $function, ...$parameters): self
    {
        return self::wrap($function, ...$parameters);
    }

    /**
     * @template C
     * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C)|C $value
     * @param mixed $parameters
     * @return Optional<C>|None|Some<C>
     */
    private static function wrap(mixed $value, ...$parameters): self|None|Some
    {
        return match (/** @infection-ignore-all */ true) {
            isCallable($value) => self::tryTo($value, ...$parameters),
            $value instanceof self => $value,
            isNull($value) => None::create(),
            default => Some::from($value)
        };
    }

    /**
     * @template C
     * @param (callable(mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=, mixed=):C) $value
     * @param mixed[] $params
     * @return Optional<C>
     */
    private static function tryTo(callable $value, ...$params): Optional
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
     * @template D
     * @param D $defaultValue
     * @return T|D
     */
    abstract public function getOrElse(mixed $defaultValue): mixed;

    /**
     * @param string $message
     * @return T
     * @throws RuntimeException
     */
    abstract public function getOrFail(string $message = '');

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

    /**
     * @return Optional<T>
     */
    public function always(callable $function): Optional
    {
        return self::try($function, $this);
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
