<?php

declare(strict_types=1);

namespace j45l\maybe\DoTry;

use j45l\maybe\Context\Context;
use j45l\maybe\None;

/**
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
}
