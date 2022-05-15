<?php

declare(strict_types=1);

namespace j45l\maybe;

use Closure;
use j45l\maybe\DoTry\Failure;

use function Functional\first;
use function Functional\invoke;
use function Functional\map;
use function Functional\partial_left;
use function Functional\some;
use function j45l\maybe\DoTry\doTry;

function lift(callable $callable): Closure
{
    return static function (...$parameters) use ($callable) {
        $isNone = static function (Maybe $maybe) {
            return $maybe instanceof None;
        };

        $isFailure = static function (Maybe $maybe) {
            return $maybe instanceof Failure;
        };

        $buildLifted = static function ($parameters) use ($isNone, $isFailure, $callable) {
            /** @infection-ignore-all */
            switch (true) {
                case some($parameters, $isFailure):
                    return first($parameters, $isFailure);
                case some($parameters, $isNone):
                    return None::create();
                default:
                    return doTry(partial_left($callable, ...invoke($parameters, 'get')))
                ;
            }
        };

        $liftParameters = map($parameters, function ($parameter) {
            return $parameter instanceof Maybe ? $parameter : Some::from($parameter);
        });

        return $buildLifted(invoke($liftParameters, 'resolve'));
    };
}
