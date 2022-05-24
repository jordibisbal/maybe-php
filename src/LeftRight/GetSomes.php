<?php

declare(strict_types=1);

namespace j45l\maybe\Functions;

use j45l\maybe\Maybe\Some;

use function Functional\filter;
use function Functional\map;

/**
 * @param iterable<mixed> $values
 * @param mixed $defaultValue
 * @phpstan-return mixed[]
 */
function getSomes(iterable $values, $defaultValue = null): array
{
    return filter(
        map(
            $values,
            function ($value) use ($defaultValue) {
                switch (true) {
                    case $value instanceof Some:
                        return $value->get();
                    default:
                        return $defaultValue;
                }
            }
        ),
        function ($value) {
            return $value !== null;
        }
    );
}
