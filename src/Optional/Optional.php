<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\functional\Functor;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\ThrowableReason;
use j45l\maybe\Maybe\Maybe;
use Throwable;

/**
 * @template T
 */
abstract class Optional implements Functor
{
    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @param mixed $value
     * @param mixed $parameters
     * @return Optional<T>
     */
    public static function do($value, ...$parameters): self
    {
        /** @infection-ignore-all */
        switch (true) {
            case is_callable($value):
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
     * @param mixed $defaultValue
     * @param string|int|array<string|int> $propertyName
     * @return mixed
     */
    abstract public function takeOrElse($propertyName, $defaultValue);

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

    //endregion
}
