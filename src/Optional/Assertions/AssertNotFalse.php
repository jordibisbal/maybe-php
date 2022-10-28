<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Exception;
use j45l\maybe\Optional\Assertions\AssertionFailed;

use function is_null as isNull;

/**
 * @template T of Optional
 * @param T $optional
 * @return T
 * @throws Exception
 */
function assertNotFalse(Optional $optional, string|Exception $message = null): Optional
{
    $throwException = static fn () => match (true) {
        $message instanceof Exception => throw $message,
        isNull($message) => throw AssertionFailed::becauseOptionalIsNotFalse($optional),
        default => throw AssertionFailed::because($message)
    };

    return $optional->getOrElse(null) === false ? $throwException() : $optional;
}
