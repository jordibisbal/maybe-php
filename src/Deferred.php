<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;
use j45l\either\Result\ThrowableReason;
use Throwable;

/**
 * @template T
 * @extends Either<T>
 */
class Deferred extends Either
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
     * @return Either<T>
     */
    public function resolve(...$parameters): Either
    {
        /** @var Deferred<T> $either */
        $either = $this->withParameters(...$parameters);
        try {
            return Either::build(
                ($either->callable)(...$either->context()->parameters()->asArray()),
                $either->context
            );
        } catch (Throwable $throwable) {
            return Either::buildFailure(
                ThrowableReason::fromThrowable($throwable),
                $either->context()->push($either)
            );
        }
    }

    /**
     * @param T $value
     * @return Either<T>
     */
    public function andThen($value): Either
    {
        return $this->resolve()->andThen($value);
    }

    /**
     * @return Either<T>
     */
    public function pipe(callable $callable): Either
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
     * @param T $value
     * @return Either<T>
     */
    public function orElse($value): Either
    {
        return $this->resolve()->orElse($value);
    }
}
