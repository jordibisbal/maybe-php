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
     * @return Either<T>
     */
    public function resolve(): Either
    {
        try {
            return Either::build(
                ($this->callable)(...$this->context->parameters()->asArray()),
                $this->context
            );
        } catch (Throwable $throwable) {
            return Either::buildFailure(
                ThrowableReason::fromThrowable($throwable),
                $this->context->push($this)
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
