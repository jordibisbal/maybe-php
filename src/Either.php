<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;
use j45l\either\Context\Parameters;
use j45l\either\Context\Trail;
use j45l\either\Tags\Tag;
use j45l\either\Tags\TagCreator;
use j45l\functional\Functor;

/** @template T */
abstract class Either implements Functor
{
    /** @var Context<T> */
    protected $context;

    /** @param Context<T>|null $context */
    protected function __construct(Context $context = null)
    {
        $this->context = $context ?? Context::create();
    }

    /**
     * @param mixed $value
     * @param Context<T> $context
     * @return Either<T>
     */
    final protected static function build($value, Context $context): Either
    {
        /** @infection-ignore-all */
        switch (true) {
            case $value instanceof self:
                return $value->cloneWith($context);
            case is_callable($value):
                return new Deferred($value, $context);
            case is_null($value):
                return new None($context);
            default:
                return new Some($value, $context);
        }
    }

    /** @return Success<T> */
    public static function start(): Success
    {
        return Success::create();
    }

    /** @return Either<T> */
    public function map(callable $callable): Functor
    {
        return new Deferred($callable, Context::fromParameters(Parameters::create($this)));
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
     * @return Trail<T>
     */
    public function trail(): Trail
    {
        return $this->context->push($this->resolve())->trail();
    }

    /**
     * @param mixed[] $parameters
     * @return Either<T>
     */
    public function resolve(...$parameters): Either
    {
        return $this->withParameters(...$parameters);
    }

    /**
     * @return Deferred<T>
     */
    public function pipe(callable $callable): Either
    {
        return new Deferred($callable, $this->context()->push($this)->withParameters($this->resolve()));
    }

    /**
     * @return Context<T>
     */
    public function context(): Context
    {
        return $this->context;
    }

    /**
     * @param mixed $value
     * @return Either<T>
     */
    public function then($value): Either
    {
        return $this->next($value);
    }

    /**
     * @param mixed $value
     * @return Either<T>
     */
    public function next($value): Either
    {
        return self::build($value, $this->context()->push($this->resolve()));
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getOrElse($value)
    {
        return $value;
    }

    /**
     * @param string|Tag $tag
     * @param mixed $value
     * @return Either<T>
     */
    public function tagNext($tag, $value): Either
    {
        return self::build(
            $value,
            $this->withTag(TagCreator::from($tag))->context()->push($this->resolve())
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $value
     * @return Either<T>
     */
    public function orElse($value): Either
    {
        return $this;
    }

    /**
     * @param Context<T>|null $context
     * @return static<T>
     */
    final protected function cloneWith(Context $context = null): self
    {
        $new = clone $this;

        $new->context = $context ?? $this->context;

        return $new;
    }

    /**
     * @param array<mixed> $parameters
     * @return static<T>
     */
    final public function with(...$parameters): Either
    {
        return $this->cloneWith($this->context->withParameters(...$parameters));
    }

    /**
     * @param Tag | int | string $tag
     * @return Either<T>
     */
    final public function withTag($tag): Either
    {
        return $this->resolve()->cloneWith(
            $this->context()->push($this->resolve())
                ->withTag(TagCreator::from($tag))
        );
    }

    /**
     * @param mixed[] $parameters
     * @return Either<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function withParameters(...$parameters): Either
    {
        /** @infection-ignore-all */
        switch (true) {
            case count($parameters) > 0:
                return $this->with(...$parameters);
            default:
                return $this;
        }
    }
}
