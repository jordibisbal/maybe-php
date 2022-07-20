<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use RuntimeException;
use Throwable;

final class ThrowableReason extends Reason
{
    /** @var Throwable */
    private $throwable;

    private static function upcast(self $reason, Throwable $throwable = null): ThrowableReason
    {
        $reason->throwable = $throwable ?? (new RuntimeException($reason->toString()));

        return $reason;
    }

    public static function fromString(string $reason): Reason
    {
        return self::upcast(parent::fromString($reason));
    }

    public static function fromThrowable(Throwable $throwable): ThrowableReason
    {
        return self::upcast(parent::fromString($throwable->getMessage()), $throwable);
    }

    public function throwable(): Throwable
    {
        return $this->throwable;
    }
}
