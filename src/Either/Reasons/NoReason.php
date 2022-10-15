<?php

namespace j45l\maybe\Either\Reasons;

use j45l\maybe\Either\Reason;
use JetBrains\PhpStorm\Pure;

final class NoReason implements Reason
{
    #[Pure] private function __construct()
    {
    }

    #[Pure] public static function create(): self
    {
        return new self();
    }

    #[Pure] public function toString(): string
    {
        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return 'No reason.';
    }
}
