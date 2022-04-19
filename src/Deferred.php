<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\maybe\Context\Context;
use j45l\maybe\DoTry\ThrowableReason;
use Throwable;

/**
 * @template T
 * @extends Maybe<T>
 */
class Deferred extends Maybe
{
    /** @var callable */
    protected $callable;

    /** @param Context<T> $context */
    protected function __construct(callable $value, Context $context)
    {
        parent::__construct($context);
        $this->callable = $value;
    }

    /**
     * @return Deferred<T>
     */
    public static function create(callable $value): Deferred
    {
        return new self($value, Context::create());
    }

    /**
     * @param mixed[] $parameters
     * @return Maybe<T>
     */
    public function resolve(...$parameters): Maybe
    {
        /** @var Deferred<T> $maybe */
        $maybe = $this->withParameters(...$parameters);
        try {
            return Maybe::build(
                ($maybe->callable)(...$maybe->context()->parameters()->asArray()),
                $maybe->context
            );
        } catch (Throwable $throwable) {
            return Maybe::buildFailure(
                ThrowableReason::fromThrowable($throwable),
                $maybe->context()->push($maybe)
            );
        }
    }

    /**
     * @param T $value
     * @return Maybe<T>
     */
    public function andThen($value): Maybe
    {
        return $this->resolve()->andThen($value);
    }

    /**
     * @return Maybe<T>
     */
    public function pipe(callable $callable): Maybe
    {
        return $this->resolve()->pipe($callable);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getOrElse($value)
    {
        return $this->resolve()->getOrElse($value);
    }

    /**
     * @param mixed $default
     * @param string|int|array<string|int> $propertyName
     * @return mixed
     */
    public function takeOrElse($propertyName, $default)
    {
        return $this->resolve()->takeOrElse($propertyName, $default);
    }

    /**
     * @param T $value
     * @return Maybe<T>
     */
    public function orElse($value): Maybe
    {
        return $this->resolve()->orElse($value);
    }
}
