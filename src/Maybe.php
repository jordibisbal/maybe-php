<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\functional\Functor;
use j45l\maybe\Context\Context;
use j45l\maybe\Context\ContextAware;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\DoTry\ThrowableReason;

/**
 * @template T
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class Maybe implements Functor
{
    /** @phpstan-use ContextAware<T> */
    use ContextAware;

    /** @param Context<T>|null $context */
    protected function __construct(Context $context = null)
    {
        $this->context = $context ?? Context::create();
    }

    /**
     * @param mixed $value
     * @param Context<T> $context
     * @return Maybe<T>
     */
    final protected static function build($value, Context $context): Maybe
    {
        /** @infection-ignore-all */
        switch (true) {
            case $value instanceof self:
                return $value->cloneWith($context);
            case is_callable($value):
                return new Deferred($value, $context);
            default:
                return new Some($value, $context);
        }
    }

    /** @return Success<T> */
    public static function begin(): Success
    {
        return Success::create();
    }

    /**
     * @param Context<T> $context
     * @return Failure<T>
     */
    protected static function buildFailure(ThrowableReason $throwable, Context $context): Failure
    {
        return new Failure($context, $throwable);
    }

    /**
     * @param mixed[] $parameters
     * @return Maybe<T>
     */
    public function resolve(...$parameters): Maybe
    {
        return $this->withParameters(...$parameters)->doResolve();
    }

    /**
     * @return Maybe<T>
     */
    protected function doResolve(): Maybe
    {
        $new = clone($this);

        $new->context = $new->context->tag($this);
        return $new;
    }

    /**
     * @template R
     * @param callable(Some<T>): Maybe<R>|callable(Some<T>): R $callable
     * @return Maybe<R>
     */
    public function pipe(callable $callable): Maybe
    {
        return $this->next($callable, $this); /** @phpstan-ignore-line */
    }

    /**
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sink(callable $callable): Maybe
    {
        return $this;
    }

    /**
     * @param mixed $value
     * @param array<mixed> $parameters
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function andThen($value, ...$parameters): Maybe
    {
        return $this->doResolve()->next($value, ...$parameters);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $value
     * @param array<mixed> $parameters
     * @return Maybe<T>
     */
    public function orElse($value, ...$parameters): Maybe
    {
        return $this->resolve($parameters);
    }

    /**
     * @param mixed $value
     * @param array<mixed> $parameters
     * @return Maybe<T>
     */
    public function next($value, ...$parameters): Maybe
    {
        /** @infection-ignore-all */
        switch (true) {
            case count($parameters) > 0:
                return $this->withParameters(...$parameters)->next($value);
            default:
                return self::build($value, $this->track())->doResolve();
        }
    }


    /**
     * @param mixed $default
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrElse($default)
    {
        return $default;
    }

    /**
     * @param mixed $default
     * @param string|int|array<string|int> $propertyName
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function takeOrElse($propertyName, $default)
    {
        return $default;
    }

    /**
     * @param callable(Some<T>): Maybe<T> $function
     * @return Maybe<T>
     */
    abstract public function map(callable $function): Functor;
}
