<?php

declare(strict_types=1);

namespace j45l\maybe\Either\Reasons;

use j45l\maybe\Either\Reason;
use j45l\maybe\Maybe\None;
use j45l\maybe\Optional\Optional;

final class FailureReason implements Reason
{
    private string $reason;

    /** @var Optional<mixed> */
    private Optional $subject;

    private function __construct(string $reason)
    {
        $this->subject = None::create();
        $this->reason = $reason;
    }

    /** @return static */
    public static function fromString(string $reason): FailureReason
    {
        return new self($reason);
    }

    /**
     * @param string $format Sprintf format
     * @param mixed ...$values
     */
    public static function fromFormatted(string $format, ...$values): FailureReason
    {
        return self::fromString(sprintf($format, ...$values));
    }

    public function toString(): string
    {
        return $this->reason;
    }

    /**
     * @param Optional<mixed> $subject
     */
    public function withSubject(Optional $subject): FailureReason
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
