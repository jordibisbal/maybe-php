<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\maybe\Optional\Left;
use j45l\maybe\Optional\NonValued;

/**
 * @extends Either<mixed>
 */
final class Failure extends Either
{
    use NonValued;
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
}
