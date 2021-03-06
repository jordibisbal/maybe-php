## also(callable $callable, mixed ...$parameters): Optional
Return a callable, so it calls $callable with an Optional and $parameters, the return of this callable is ignored and
the Optional is return (unless an exception happens, then a Failure is returned)

```php
$value = 0;
$effect = static function (Optional $optional) use (&$value) {
    $value = $optional->getOrElse(0) + 1;
    return None::create();
};

$original = Some::from(41);
$some = $original->andThen(also($effect));

self::assertSame($original, $some);
self::assertEquals(42, $value);
```

## lift(callable $callable): Optional

Returns a new function wrapped in an *Optional*, on call the function parameters are also briefly lifted,
if any of the parameters is *None* the lifted function will return *None* (unless any is *Failure*),
if any of the parameters is *Failure* the lifted function will return *Failure*.

```php
$function = function ($one, $another) {
    return $one + $another;
};

$some = lift($function)(41, Some::from(1));
self::assertInstanceOf(Some::class, $some);
self::assertEquals(42, $some->get());

$none = lift($function)(Some::from(41), None::create());
self::assertInstanceOf(None::class, $none);
self::assertNotInstanceOf(Failure::class, $none);

$some = lift($function)(Some::from(41), Failure::create());
self::assertInstanceOf(Failure::class, $some);

$failure = lift($function)(None::create(), Failure::create());
self::assertInstanceOf(Failure::class, $failure);
```