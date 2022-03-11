<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;
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
     * @param T $value
     * @return Either<T>
     */
    public function orElse($value): Either
    {
        return $this->resolve()->orElse($value);
    }

    /**
     * @param mixed[] $parameters
     * @return Either<T>
     */
    public function resolve(...$parameters): Either
    {
        /** @var Deferred<T> $either */
        $either = $this->overrideParameters(...$parameters);

        try {
            return Either::build(
                ($either->callable)(...$either->context->parameters()->asArray()),
                $either->context
            );
        } catch (Throwable $throwable) {
            return Either::buildFailure(
                ThrowableReason::fromThrowable($throwable),
                $either->context->push($either)
            );
        }
    }

    /**
     * @param T $value
     * @return Either<T>
     */
    public function then($value): Either
    {
        return $this->resolve()->then($value);
    }

    /**
     * @return Either<T>
     */
    public function pipe(callable $callable): Either
    {
        return $this->resolve()->pipe($callable);
    }
}
