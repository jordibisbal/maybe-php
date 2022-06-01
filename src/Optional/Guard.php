<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Either;
use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use Throwable;

/**
 * @throws Throwable
 * @phpstan-return Either<void>
 */
function guard(bool $guard, Throwable $throwable = null): Either
{
    switch (true) {
        case $guard:
            return JustSuccess::create();
        case !is_null($throwable):
            throw $throwable;
        default:
            return Failure::create();
    }
}
