<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

class Reason
{
    /** @var string */
    private $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public static function fromString(string $reason): Reason
    {
        return new self($reason);
    }

    /**
     * @param string $format Sprintf format
     * @param mixed ...$values
     */
    public static function fromFormatted(string $format, ...$values): Reason
    {
        return new self(sprintf($format, ...$values));
    }

    public function toString(): string
    {
        return $this->reason;
    }
}
