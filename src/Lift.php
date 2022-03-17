<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
use j45l\either\Result\Failure;

use function Functional\invoke;
use function Functional\map;
use function Functional\some;

function lift(callable $callable): Closure
{
    return static function (...$parameters) use ($callable) {
        $isNone = static function (Either $either) {
            return $either instanceof None;
        };
        $isFailure = static function (Either $either) {
            return $either instanceof Failure;
        };

        $buildLifted = static function ($parameters) use ($isNone, $isFailure, $callable) {
            /** @infection-ignore-all */
            switch (true) {
                case some($parameters, $isFailure):
                    return Failure::create();
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
                return $parameter instanceof Either ? $parameter : Some::from($parameter);
            }),
            'resolve'
        ));
    };
}