<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\maybe\Optional\Left;
use j45l\maybe\Optional\NonValued;
use j45l\maybe\Optional\Optional;
use RuntimeException;

/**
 * @extends Either<mixed>
 */
final class Failure extends Either
{
    /** @use NonValued<mixed> */
    use NonValued {
        getOrRuntimeException as nonValuedGetOrRuntimeException;
    }

    /** @use Left<void> */
    use Left;

    /**
     * @var Reason
     */
    private $reason;

    private function __construct(Reason $reason)
    {
        $this->reason = $reason;
    }

    public static function create(): Failure
    {
        return new self(Reason::fromString('Unspecified reason'));
    }

    /** @return Failure */
    public static function because(Reason $reason): Failure
    {
        return new self($reason);
    }

    public function reason(): Reason
    {
        return $this->reason;
    }

    public function getOrRuntimeException(string $message = null)
    {
        $this->throwRuntimeException($message ?? $this->reason->toString(), $this->reason);
    }

    private function throwRuntimeException(string $message, Reason $reason): void
    {
        switch (/** @infection-ignore-all */ true) {
            case $reason instanceof ThrowableReason:
                throw new RuntimeException($message, 0, $reason->throwable());
            default:
                $this->nonValuedGetOrRuntimeException($message);
        }
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function assert($condition, string $message = null): Optional
    {
        return $this;
    }
}
