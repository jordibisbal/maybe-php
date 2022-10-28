<?php

declare(strict_types=1);

namespace j45l\maybe\Either\Reasons;

use j45l\maybe\Either\Reason;
use RuntimeException;
use Throwable;

final class ThrowableReason implements Reason
{
    private Throwable $throwable;

    private function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /** @return self */
    public static function fromString(string $reason): ThrowableReason
    {
        return new self(new RuntimeException($reason));
    }

    public static function fromThrowable(Throwable $throwable): ThrowableReason
    {
        return new self($throwable);
    }

    public function throwable(): Throwable
    {
        return $this->throwable;
    }

    /**
     * @param mixed $values
     * @return ThrowableReason
     */
    public static function fromFormatted(string $format, ...$values): ThrowableReason
    {
        return self::fromString(sprintf($format, ...$values));
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return $this->throwable()->getMessage();
    }
}
