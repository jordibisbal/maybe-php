<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\functional\Functor;
use j45l\maybe\Maybe;

/**
 * @extends Maybe<void>
 */
class AnotherUnhandledMaybe extends Maybe
{
    public static function create(): AnotherUnhandledMaybe
    {
        return new self();
    }

    /**
     * @param callable $function
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @returns Maybe<void>
     */
    public function map(callable $function): Functor
    {
        return $this;
    }
}
