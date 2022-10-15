<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\Reason;
use j45l\maybe\Either\Reasons\NoReason;
use JetBrains\PhpStorm\Pure;

/** @param Optional<mixed> $optional */
#[Pure] function reason(Optional $optional): Reason
{
    return match (true) {
        $optional instanceof Failure => $optional->reason(),
        default => NoReason::create(),
    };
}
