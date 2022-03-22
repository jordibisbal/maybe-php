<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\maybe\Maybe;

/** @extends Maybe<void> */
class AnotherUnhandledMaybe extends Maybe
{
    public static function create(): AnotherUnhandledMaybe
    {
        return new self();
    }
}
