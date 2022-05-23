<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use j45l\maybe\Either\Failure;

use function Functional\filter;
use function Functional\map;

/**
 * @param iterable<mixed> $values
 * @phpstan-return mixed[]
 */
function getFailureReasons(iterable $values): array
{
    return filter(
        map(
            $values,
            function ($value) {
                switch (true) {
                    case $value instanceof Failure:
                        return $value->reason()->toString();
                    default:
                        return null;
                }
            }
        ),
        function ($value) {
            return $value !== null;
        }
    );
}
