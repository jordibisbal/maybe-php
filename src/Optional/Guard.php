<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use RuntimeException;

/**
 * @phpstan-return Either<void>
 */
function guard(bool $guard, string $runtimeErrorMessage = null): Either
{
    switch (true) {
        case $guard:
            return JustSuccess::create();
        case !is_null($runtimeErrorMessage):
            throw new RuntimeException($runtimeErrorMessage);
        default:
            return Failure::create();
    }
}
