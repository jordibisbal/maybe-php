# Why

Maybe (inspired by the functional thing), helps to:
* Better handling of exception (Success/Failure).
* Discourage the use of null (None/Some).
* Simplify the control flow on errors.
* Build failsafe code
* Improve testability.

## Maybe

Is a supercharged version on the Maybe from functional programing, it provides functionality
present in such languages along some extra to solve some common use cases in php.

Some literature about, google for more:

https://www.thoughtworks.com/en-es/insights/blog/either-data-type-alternative-throwing-exceptions

https://itnext.io/either-monad-a-functional-approach-to-error-handling-in-js-ffdc2917ab2

https://functionalprogramming.medium.com/either-is-a-common-type-in-functional-languages-94b86eea325c

## Maybe Documentation

[Maybe class](resources/documentation/Maybe.md)

[Support functions](resources/documentation/Functions.md)

## Lifting

You can lift a function (closure) to *Maybe* by using *lift()*, doing so result in a function with the same signature but
return a Maybe, when invoking the lifted function, if any of the argument is a *None* or and *Failure* will return into
one, any *Some* or *Deferred* will be resolved before the original function is invoked.

```php
$lifted = function (int $first, int $second): int { return $first + $second; };
$maybe = $lifted(40, 2);

$this->assertInstanceOf(Some::class, $maybe);
$this->assertEquals(42, $maybe->get());
```