<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
use Throwable;

class Deferred extends Either
{
    protected $closure;

    protected function __construct($value, Context $context)
    {
        parent::__construct($context);
        $this->closure = $value;
    }

    public function orElse($defaultValue): Either
    {
        return $this->resolve()->orElse($defaultValue);
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

    public function then($nextValue): Either
    {
        return $this->resolve()->then($nextValue);
    }

    public function pipe(Closure $closure): Either
    {
        return $this->resolve()->pipe($closure);
    }
}
