<?php

declare(strict_types=1);

namespace j45l\maybe\Optional\Assertions;

use DomainException;
use j45l\maybe\Optional\Optional;

final class AssertionFailed extends DomainException
{
    /** @param Optional<mixed> $optional */
    public static function becauseOptionalIsNotFalse(Optional $optional): AssertionFailed
    {
        return new self(sprintf(
            'Failed asserting that a %s is not false.',
            $optional::class
        ));
    }

    public static function because(string $message): AssertionFailed
    {
        return new self($message);
    }
}
