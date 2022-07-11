<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\maybe\Maybe\None;
use j45l\maybe\Optional\Optional;

class Reason
{
    /** @var string */
    private $reason;

    /** @var Optional<mixed> */
    private $subject;

    public function __construct(string $reason)
    {
        $this->subject = None::create();
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
        return self::fromString(sprintf($format, ...$values));
    }

    /**
     * @param string $format Sprintf format
     * @param mixed ...$values
     */
    public function withFormatted(string $format, ...$values): Reason
    {
        $self = clone $this;
        $self->reason = sprintf($format, ...$values);

        return $self;
    }

    public function toString(): string
    {
        return $this->reason;
    }

    /**
     * @param Optional<mixed> $subject
     * @return static
     */
    public function withSubject(Optional $subject): self
    {
        $new = clone $this;
        $new->subject = $subject;

        return $new;
    }

    /** @return Optional<mixed> */
    public function subject(): Optional
    {
        return $this->subject;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
