
# Optionals
Just an **Optional**s collection

## Basic operation
### create
``` PHP
public static function create(array $optionals = []): self
```
### count
``` PHP
public function count(): int
```
Returns the collection item count.

### empty
``` PHP
public function empty(): bool
```
Returns whenever the collection is empty.

### head
public function head($default = null): Optional
``` PHP
public function head($default = null): Optional
```
Return the first Optional in the collection.

### items
``` PHP
public function items(): array
```
Returns the collection as an array.

### tail
``` PHP
public function head($default = null): Optional
```
Return a new **Optional** with all the current items but the first.

## Merging

### mergeSomes, mergeFailures
``` PHP
public function mergeSomes(Optional ...$optionals): self
public function mergeFailures(Optional ...$optionals): self
```
Return a new collection with the current **Optionals** elements, plus just the **Some** (or **Failure**)
elements of all the *$optionals*.

## Selection

### failureReasonStrings()
``` PHP
public function failureReasonStrings(): array
```

Return the **reason** as string of every **Failure** in the collection.

### somes, successes, nones, failures
``` PHP
public function somes(): self
public function successes(): self
public function nones(): self
public function failures(): self
```

Returns a new **Optionals** with just the given type of optionals, note that successes will
return an **Optionals** with both **JustSuccess**es and **Some**s values.

### values
``` PHP
public function values(): array
```
Returns an array with all the values of all the **Some** in the **Optionals**, other types are
ignored.
