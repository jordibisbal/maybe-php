<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\maybe\Context\Context;
use j45l\maybe\Maybe;
use j45l\maybe\None;

/**
 * @deprecated Move to v3
 * @template T
 * @extends None<T>
 */
final class Failure extends None
{
    /** @var Reason */
    private $reason;

    protected function __construct(Context $context = null, Reason $reason = null)
    {
        $this->reason = $reason ?? new Reason('Unspecified reason');

        parent::__construct($context);
    }

    /** @return Failure<T> */
    public static function create(): None
    {
        return new self();
    }

    /** @return Failure<T> */
    public static function from(Reason $reason = null): Failure
    {
        return new self(Context::create(), $reason);
    }

    public function reason(): Reason
    {
        return $this->reason;
    }

    /**
     * @return Maybe<T>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function pipe(callable $callable): Maybe
    {
        return $this;
    }

    /**
     * @return Maybe<T>
     */
    public function sink(callable $callable): Maybe
    {
        return self::build($callable, $this->track()->withParameters($this))->resolve();
    }
}
