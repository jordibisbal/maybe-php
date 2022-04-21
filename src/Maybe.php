<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\maybe\Context\Context;
use j45l\maybe\Context\Parameters;
use j45l\maybe\Context\Trail;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Success;
use j45l\maybe\DoTry\ThrowableReason;
use j45l\maybe\Tags\Tag;
use j45l\maybe\Tags\TagCreator;
use j45l\functional\Functor;

/**
 * @template T
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class Maybe implements Functor
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

    /**
     * @template TC
     * @param callable(Some<TC>): Maybe<TC> $function
     * @return Maybe<TC>
     */
    public function map(callable $function): Functor
    {
        return (new Deferred(
            $function,
            Context::fromParameters(Parameters::create($this))
        ))->resolve();
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
     * @return Maybe<T>
     */
    public function resolve(...$parameters): Maybe
    {
        return $this->withParameters(...$parameters);
    }

    /**
     * @return Maybe<T>
     */
    public function pipe(callable $callable): Maybe
    {
        return self::build($callable, $this->context()->push($this)->withParameters($this));
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
     * @return Context<T>
     */
    public function context(): Context
    {
        return $this->context;
    }

    /**
     * @param mixed $value
     * @return Maybe<T>
     */
    public function andThen($value): Maybe
    {
        return $this->next($value);
    }

    /**
     * @param mixed $value
     * @return Maybe<T>
     */
    public function next($value): Maybe
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
     * @param string|Tag $tag
     * @param mixed $value
     * @return Maybe<T>
     */
    public function tagNext($tag, $value): Maybe
    {
        return self::build(
            $value,
            $this->withTag(TagCreator::from($tag))->context()->push($this->resolve())
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $value
     * @return Maybe<T>
     */
    public function orElse($value): Maybe
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
    final public function with(...$parameters): Maybe
    {
        return $this->cloneWith($this->context->withParameters(...$parameters));
    }

    /**
     * @param Tag | int | string $tag
     * @return Maybe<T>
     */
    final public function withTag($tag): Maybe
    {
        return $this->resolve()->cloneWith(
            $this->context()->push($this->resolve())
                ->withTag(TagCreator::from($tag))
        );
    }

    /**
     * @param mixed[] $parameters
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function withParameters(...$parameters): Maybe
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
