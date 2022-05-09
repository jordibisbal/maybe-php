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
     * @return Maybe<T>
     */
    protected function doResolve(): Maybe
    {
        try {
            return Maybe::build(
                ($this->callable)(...$this->context()->parameters()->asArray()),
                $this->context()
            )->doResolve();
        } catch (Throwable $throwable) {
            return Maybe::buildFailure(ThrowableReason::fromThrowable($throwable), $this->context())->doResolve();
        }
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->doResolve()->getOrElse($default);
    }

    /**
     * @param mixed $default
     * @param string|int|array<string|int> $propertyName
     * @return mixed
     */
    public function takeOrElse($propertyName, $default)
    {
        return $this->doResolve()->takeOrElse($propertyName, $default);
    }
}
