<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Either\Reasons\ThrowableReason;
use j45l\maybe\Optional\Left;
use j45l\maybe\Optional\NonValued;
use RuntimeException;
use Throwable;

/**
 * @template T
 * @extends Either<T>
 */
final class Failure extends Either
{
    /** @use NonValued<mixed> */
    use NonValued {
        runtimeException as nonValuedRuntimeException;
    }

    /** @use Left<void> */
    use Left;

    /**
     * @var Reason
     */
    private Reason $reason;

    private function __construct(Reason $reason)
    {
        $this->reason = $reason;
    }

    /** @return Failure<T> */
    public static function create(): Failure
    {
        return new self(FailureReason::fromString('Unspecified reason'));
    }

    /** @return Failure<T> */
    public static function because(Reason $reason): Failure
    {
        return new self($reason);
    }

    public function reason(): Reason
    {
        return $this->reason;
    }

    /**
     * @throws Throwable
     */
    public function getOrFail(RuntimeException|string $message = null): void
    {
        throw match (/** @infection-ignore-all */ true) {
            $message instanceof RuntimeException => $message,
            default => $this->runtimeException($message ?? $this->reason->toString(), $this->reason)
        };
    }

    private function runtimeException(string $message, Reason $reason): RuntimeException
    {
        return match (/** @infection-ignore-all */ true) {
            $reason instanceof ThrowableReason => new RuntimeException($message, 0, $reason->throwable()),
            default => $this->nonValuedRuntimeException($message)
        };
    }
}
