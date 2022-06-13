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

        $liftParameters = function ($parameters) {
            return map($parameters, function ($parameter) {
                return Optional::do($parameter);
            });
        };

        $sinkParameters = function ($parameters) {
            return map($parameters, function (Some $parameter) {
                return $parameter->get();
            });
        };

        $buildLifted = static function ($callable, ...$parameters) use ($isNone, $isFailure, $sinkParameters) {
            switch (/** @infection-ignore-all */ true) {
                case some($parameters, $isFailure):
                    return first($parameters, $isFailure);
                case some($parameters, $isNone):
                    return None::create();
                default:
                    return Optional::do(partial_left($callable, ...($sinkParameters($parameters))))
                ;
            }
        };

        return $buildLifted($callable, ...$liftParameters($parameters));
    };
}
