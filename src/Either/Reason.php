<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

interface Reason
{
    public function toString(): string;

    public function __toString(): string;
}
