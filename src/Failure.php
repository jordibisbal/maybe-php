<?php

declare(strict_types=1);

namespace j45l\either;

class Failure extends None
{
    /** @var Reason */
    private $reason;

    protected function __construct(Context $context, Reason $failure = null)
    {
        $this->reason = $failure ?? new Reason('Unspecified reason');

        parent::__construct($context);
    }

    public static function from(Reason $reason = null): Failure
    {
        return new self(Context::create(), $reason);
    }

    public function reason(): Reason
    {
        return $this->reason;
    }
}
