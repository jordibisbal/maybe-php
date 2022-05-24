<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use RuntimeException;
use Throwable;

final class ThrowableReason extends Reason
{
    /** @var Throwable */
    private $throwable;

    public function __construct(string $reason, Throwable $throwable)
    {
        parent::__construct($reason);
        $this->throwable = $throwable;
    }

    public static function fromString(string $reason): Reason
    {
        return new self($reason, new RuntimeException($reason));
    }

    public static function fromThrowable(Throwable $throwable): ThrowableReason
    {
        return new self($throwable->getMessage(), $throwable);
    }

    public function throwable(): Throwable
    {
        return $this->throwable;
    }
}
