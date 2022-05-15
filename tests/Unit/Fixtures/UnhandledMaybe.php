<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\functional\Functor;
use j45l\maybe\Maybe;

/** @extends Maybe<void> */
class UnhandledMaybe extends Maybe
{
    public static function create(): UnhandledMaybe
    {
        return new self();
    }

    /**
     * @param callable $function
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Maybe<void>
     */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
