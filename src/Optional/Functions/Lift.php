<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Closure;
use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;

use function Functional\first;
use function Functional\map;
use function Functional\partial_left;
use function Functional\some;

function lift(callable $callable): Closure
{
    return static function (...$parameters) use ($callable) {
        $isNone = static function (Optional $maybe) {
            return $maybe instanceof None;
        };

        $isFailure = static function (Optional $maybe) {
            return $maybe instanceof Failure;
        };

        $liftParameters = static function ($parameters) {
            return map($parameters, function ($parameter) {
                return Optional::try(static fn () => $parameter);
            });
        };

        $sinkParameters = static function ($parameters) {
            return map($parameters, function (Some $parameter) {
                return $parameter->get();
            });
        };

        $buildLifted = static function ($callable, ...$parameters) use ($isNone, $isFailure, $sinkParameters) {
            return match (true) {
                some($parameters, $isFailure) => first($parameters, $isFailure),
                some($parameters, $isNone) => None::create(),
                default => Optional::try(partial_left($callable, ...($sinkParameters($parameters))))
            };
        };

        return $buildLifted($callable, ...$liftParameters($parameters));
    };
}
