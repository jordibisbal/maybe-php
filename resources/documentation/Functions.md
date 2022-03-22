## lift(callable $callable): Maybe

Returns a new function wrapped in an *Maybe*, on call the function parameters are also briefly lifted,
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
