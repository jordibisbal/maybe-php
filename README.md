# Why

Either (inspired by the functional thing), helps to:
* Get rid of exceptions (Success/Failure).
* Discourage the use of null (None/Some).
* Simplify the control flow on errors.
* Build failsafe code
* Improve testability.

## Creating

Every either must be created using its appropriate class:

* None::create()
* Some::from($value)
* Either::do(Closure $closure)
* Success::create()
* Failure::create()

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

Forces an optional to be resolved, return itself but on Deferred, an *Either* from its closure execution return value is returned.

```php
Either::do(function($customer) use ($em) { $em->insert($customer)})->with($customer)
    // The parameters/context ($customer) is passed to orElse(), so does not need to
    // be provided again, although you could override that by using a second with().         
    ->orElse(function($customer) use ($me) { $em->update})
    // The value is Deferred or a Success
    ->resolve()
    // The value Either a Failure or a Success
;
```
If *resolve()* were not caller, the second closure would not be called (lazy).

#### context(): Context

Returns the context of the *Either*, i.e. its trail and parameters

#### static do(Closure $closure): Deferred

Returns a *Deferred* from *$closure* (with the current context).

#### map(Closure $closure): Functor

Maps the *Either* value (i.e. calls *closure* with the *Either* as parameter).
Mapping a *None* results in a *None* and the *closure* is not evaluated.

#### next($nextValue): Either

Returns an *Either* from *$nextValue* (with the current context).

#### orElse($defaultValue): Either

Returns an *Either* from *$nextValue* (with the current context) if current Either is a *None*,
 otherwise returns itself.

#### pipe(Closure $closure): Either

Returns a *Deferred* from *closure* using current Either as context parameters and using curren trail.

#### resolve(): Either

On *Deferred*, its closure is evaluated and an *Either* from the evaluation result is returned. If the closure throws a *Throwable*, a *Failure* with the *Throwable* as reason is returned

#### then($nextValue): Either

Returns an *Either* from *$nextValue* (with the current context) if the current *Either* is not a *None*, otherwise returns itself.

#### trail(): Trail

Return the trail of *Either* adding itself at the end.

#### with(...$parameters): Either

Returns a clone of the *Either* changing the parameters on its context

## Other classes

### Deferred

A deferred *Either* that must be resolved in order to its *Closure* to be evaluated (called), 
a new *Either* from the *Closure* return is returned.

### Failure

A *Failure* is a *None* used for failed operations

#### from(Reason $reason = null): Failure

Returns a *Failure* with the given *$reason*

#### reason(): Reason

Returns the *Failure* reason

### None

Absence of value, equivalent to null (but without messing with the interface)

### Some

An *Either* with a value.

When cloned, if the value is an object, it is also cloned (not a deep clone, if desired, the value itself should do so)

#### from($value): Some

Returns a new *Some* from the *$value*, never modifies nor evaluates *$value*.

#### value()

Return the value

### Success

An *Either*(*Success* with true value) to signal successful operations 

 ![Class diagram](classDiagram.png)