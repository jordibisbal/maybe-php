# Why

Either (inspired by the functional thing), helps to:
* Get rid of exceptions (Succeed/Failure).
* Discourage the use of null (None/Some).
* Simplify the control flow on errors.
* Build failsafe code
* Improve testability.

## Creating

Every either must be created using its appropriate class:

* None::create()
* Some::from($value)
* Either::do(Closure $closure)
* Succeed::create()
* Failed::create()

*Either::next*, *Either::orElse*, *Either::pipe*, *Either::then* and *Deferred::resolve* methods will guess the *Either* kind to be created, by the following rules:

* *Either*: A clone of the *Either*
* *Closure*: A *Deferred*
* *null*: *None*
* Otherwise: *Some*

## Either

Base class for all *Either*s

An *Either* as a *Context* that contains the *Parameters* that will use to call *Deferred* closures (if any) and a *Trail* of the evaluated *Either*s.

A new *Either* with new *Parameters* can be changed by *Either::with* method, *Trail* is readonly.

#### resolve(): Either

Forces an optional to be resolved, return itself but on Deferred an *Either* from its closure execution return value is returned.

```php
Either::do(function($customer) use ($em) { $em->insert($customer)})->with($customer)
    // The parameters/context ($customer) is passed to orElse(), so does not need to
    // be provided again, although you could override that by using a second with().         
    ->orElse(function($customer) use ($me) { $em->update})
    ->resolve()
;
```
If *resolve()* were not caller, the second closure would not be called (lazy).

#### context(): Context

Returns the context of the *Either*, i.e. its trail and parameters

#### do(Closure $closure): Deferred

Returns a *Deferred* from *$closure* (with the current context).

#### next($nextValue): Either

Returns an *Either* from *$nextValue* (with the current context).

#### orElse($defaultValue): Either

Returns an *Either* from *$nextValue* (with the current context) if current Either is a *None*,
 otherwise returns itself.

#### pipe(Closure $closure): Either

Returns a *Deferred* from *closure* using current Either as context parameters and using curren trail.

#### resolve(): Either

On *Deferred*, its closure is evaluated and an *Either* from the evaluation result is returned. If the closure throws a *Throwable*, a *Failed* with the *Throwable* as reason is returned

#### then($nextValue): Either

Returns an *Either* from *$nextValue* (with the current context) if the current *Either* is not a *None*, otherwise returns itself.

#### trail(): Trail

Return the trail of *Either* adding itself at the end.

#### with(...$parameters): Either

Returns a clone of the *Either* changing the parameters on its context

## Other classes

### Deferred

A deferred *Either*, that is an *Either* that must be resolved in order to its *Closure* to be evaluated (called), a new *Either* from the *Closure* return is returned

### Failed

A *Failed* is a *None* used for failed operations

#### from(Reason $reason = null): Failed

Returns a *Failed* with the given *$reason*

#### reason(): Reason

Returns the *Failed* reason

### None

Absence of value, equivalent to null (but without messing with the interface)

### Some

An *Either* with a value.

When cloned, if the value is an object it is also cloned (not a deep clone, if desired, the value itself should do so)

#### from($value): Some

Returns a new *Some* from the *$value*, never modifies nor evaluates *$value*.

#### value()

Return the value

### Succeed

An *Either* to signal successful operations 
