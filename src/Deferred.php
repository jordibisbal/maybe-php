<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
use Throwable;

class Deferred extends Either
{
    /** @var Closure */
    protected $closure;

    protected function __construct(Closure $value, Context $context)
    {
        parent::__construct($context);
        $this->closure = $value;
    }

    public static function create(Closure $value): Deferred
    {
        return new self($value, Context::create());
    }

    public function orElse($value): Either
    {
        return $this->resolve()->orElse($value);
    }

    public function resolve(): Either
    {
        try {
            return Either::build(
                ($this->closure)(...$this->context->parameters()->asArray()),
                $this->context
            );
        } catch (Throwable $throwable) {
            return Either::buildFailure(
                ThrowableReason::fromThrowable($throwable),
                $this->context->push($this)
            );
        }
    }

    /** @param mixed $value */
    public function then($value): Either
    {
        return $this->resolve()->then($value);
    }

    public function pipe(Closure $closure): Either
    {
        return $this->resolve()->pipe($closure);
    }
}
