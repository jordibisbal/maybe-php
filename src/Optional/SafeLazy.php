<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;

use function j45l\functional\value;

/**
 * @template T
 * @param T|callable():T $value
 * @phpstan-return Closure():Optional<T>
 */
function safeLazy($value): Closure
{
    return static function (...$parameters) use ($value) {
        return Optional::do(value($value), ...$parameters);
    };
}
