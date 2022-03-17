<?php

declare(strict_types=1);

namespace j45l\either\Result;

class Reason
{
    /** @var string */
    private $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public static function from(string $reason): Reason
    {
        return new self($reason);
    }

    public function toString(): string
    {
        return $this->reason;
    }
}
