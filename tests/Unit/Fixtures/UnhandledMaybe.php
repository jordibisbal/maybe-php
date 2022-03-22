<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\maybe\Maybe;

/** @extends Maybe<void> */
class UnhandledMaybe extends Maybe
{
    public static function create(): UnhandledMaybe
    {
        return new self();
    }
}
