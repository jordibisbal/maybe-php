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
use j45l\maybe\Maybe\Maybe;
use Throwable;

use function get_class as getClass;
use function is_callable as isCallable;

/**
 * @template T
 */
abstract class Optional implements Functor
{
    /** @use OptionalOn<T> */
    use OptionalOn;

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @param mixed $value
     * @param mixed $parameters
     * @return Optional<T>
     */
    public static function do($value, ...$parameters): self
    {
        switch (/** @infection-ignore-all */ true) {
            case isCallable($value):
                return self::callableDo($value, ...$parameters);
            default:
                return Maybe::someWrap($value);
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
            return Maybe::someWrap($value(...$params));
        } catch (Throwable $throwable) {
            return Failure::because(ThrowableReason::fromThrowable($throwable));
        }
    }

    //region (Non)Valued

    /**
     * @param mixed $defaultValue
     * @return mixed
     */
    abstract public function getOrElse($defaultValue);

    /**
     * @param string $message
     * @return T
     */
    abstract public function getOrRuntimeException(string $message = '');

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
        return Failure::because(Reason::fromString($message ?? 'failed assertion'));
    }

    //endregion

    //region Optional

    /**
     * @param mixed $value
     * @return Optional<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    abstract public function andThen($value): Optional;

    /**
     * @param mixed $value;
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Optional<T>
     */
    abstract public function orElse($value): Optional;

    /** @return Either<T> */
    public function toEither(): Either
    {
        switch (/** @infection-ignore-all */ true) {
            case $this instanceof Success:
                return JustSuccess::create();
            default:
                return Failure::because(Reason::fromString(sprintf('From %s', getClass($this))));
        }
    }
    //endregion
}
