<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
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
            case $value instanceof Closure:
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
    public function map(Closure $closure): Functor
    {
        return new Deferred($closure, Context::fromParameters(Parameters::create($this)));
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
     * @return Either<T>
     */
    public function resolve(): Either
    {
        return $this;
    }

    /**
     * @return Deferred<T>
     */
    public function pipe(Closure $closure): Either
    {
        return new Deferred($closure, $this->context()->push($this)->withParameters($this->resolve()));
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
}
