<?php

declare(strict_types=1);

namespace j45l\maybe;

use Closure;
use j45l\maybe\DoTry\Failure;

use function Functional\first;
use function Functional\invoke;
use function Functional\map;
use function Functional\some;

/**
 * @deprecated Move to v3
 */
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
                    return Deferred::create(static function (...$parameters) use ($callable) {
                        return $callable(...$parameters);
                    })->resolve(...invoke($parameters, 'get'));
            }
        };

        return $buildLifted(invoke(
            map($parameters, function ($parameter) {
                return $parameter instanceof Maybe ? $parameter : Some::from($parameter);
            }),
            'resolve'
        ));
    };
}
