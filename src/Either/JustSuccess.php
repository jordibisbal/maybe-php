<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\functional\Functor;
use j45l\maybe\LeftRight\NonValued;
use j45l\maybe\LeftRight\Right;

/**
 * @extends Either<mixed>
 */
final class JustSuccess extends Either implements Success
{
    use NonValued;
    use Right;

    private function __construct()
    {
    }

    public static function create(): JustSuccess
    {
        return new self();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map(callable $function): Functor
    {
        return $this;
    }
}