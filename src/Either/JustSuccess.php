<?php

declare(strict_types=1);

namespace j45l\maybe\Either;

use j45l\functional\Functor;
use j45l\maybe\Optional\NonValued;
use j45l\maybe\Optional\Right;

/**
 * @template T
 * @extends Either<T>
 * @implements Success<T>
 */
final class JustSuccess extends Either implements Success
{
    /** @use NonValued<mixed> */
    use NonValued;
    /** @use Right<mixed> */
    use Right;

    private function __construct()
    {
    }

    /** @return JustSuccess<T> */
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
