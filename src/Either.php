<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
use j45l\either\Context\Context;
use j45l\either\Context\Parameters;
use j45l\either\Context\Trail;
use j45l\either\Tags\Tag;
use j45l\functional\Functor;

abstract class Either implements Functor
{
    /** @var Context */
    protected $context;

    protected function __construct(Context $context = null)
    {
        $this->context = $context ?? Context::create();
    }

    /** @param mixed $value */
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

    public static function start(): Success
    {
        return Success::create();
    }

    /** @phpstan-return Either */
    public function map(Closure $closure): Functor
    {
        return new Deferred($closure, Context::fromParameters(Parameters::create($this)));
    }

    protected static function buildFailure(ThrowableReason $throwable, Context $context): Failure
    {
        return new Failure($context, $throwable);
    }

    public function trail(): Trail
    {
        return $this->context->push($this->resolve())->trail();
    }

    public function resolve(): Either
    {
        return $this;
    }

    public function pipe(Closure $closure): Either
    {
        return new Deferred($closure, $this->context()->push($this)->withParameters($this->resolve()));
    }

    public function context(): Context
    {
        return $this->context;
    }

    /** @param mixed $value */
    public function then($value): Either
    {
        return $this->next($value);
    }

    /** @param mixed $value */
    public function next($value): Either
    {
        return self::build($value, $this->context()->push($this->resolve()));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $value
     */
    public function orElse($value): Either
    {
        return $this;
    }

    /** @return static */
    final protected function cloneWith(Context $context = null): self
    {
        $new = clone $this;

        $new->context = $context ?? $this->context;

        return $new;
    }

    /**
     * @param array<mixed> $parameters
     * @return static
     */
    final public function with(...$parameters): Either
    {
        return $this->cloneWith($this->context->withParameters(...$parameters));
    }

    /** @param Tag | int | string $tag */
    final public function withTag($tag): Either
    {
        return $this->resolve()->cloneWith(
            $this->context()->push($this->resolve())
                ->withTag(TagCreator::from($tag))
        );
    }
}
