<?php

declare(strict_types=1);

namespace j45l\either;

class Reason
{
    private $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public static function from(string $reason): Reason
    {
        return new self($reason);
    }

    public function asString(): string
    {
        return $this->reason;
    }
}
