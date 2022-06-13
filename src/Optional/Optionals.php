<?php

declare(strict_types=1);

namespace j45l\maybe\Optional;

use Countable;
use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;

use function Functional\first;
use function Functional\map;
use function Functional\select;
use function Functional\tail;

/**
 * @template T
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class Optionals implements Countable
{
    /**
     * @var Optional<T>[]
     */
    private $optionals;

    /** @phpstan-param Optional<T>[] $optionals */
    private function __construct(array $optionals)
    {
        $this->optionals = $optionals;
    }

    /**
     * @param Optional<T>[] $optionals
     * @return self<T>
     */
    public static function create(array $optionals = []): self
    {
        return new self($optionals);
    }

    /** @return self<T> */
    public function somes(): self
    {
        return self::create(select(
            $this->optionals,
            function (Optional $optional) {
                return isSome($optional);
            }
        ));
    }

    /** @return self<T> */
    public function successes(): self
    {
        return self::create(select(
            $this->optionals,
            function (Optional $optional) {
                return isSuccess($optional);
            }
        ));
    }

    /** @return self<T> */
    public function nones(): self
    {
        return self::create(select(
            $this->optionals,
            function (Optional $optional) {
                return isNone($optional);
            }
        ));
    }

    /** @return self<T> */
    public function failures(): self
    {
        return self::create(select(
            $this->optionals,
            function (Optional $optional) {
                return isFailure($optional);
            }
        ));
    }

    /** @return string[] */
    public function failureReasonStrings(): array
    {
        return map($this->failures()->items(), function (Failure $failure) {
            return $failure->reason()->toString();
        });
    }

    /** @return Optional<T>[] */
    public function items(): array
    {
        return $this->optionals;
    }

    /** @return mixed[] */
    public function values(): array
    {
        return map(
            $this->somes()->items(),
            function (Some $some) {
                return $some->get();
            }
        );
    }

    public function empty(): bool
    {
        /** @infection-ignore-all */
        return count($this->items()) === 0;
    }

    /**
     * @param Optional<T> ...$optionals
     * @return  Optionals<T>
     */
    public function mergeSomes(Optional ...$optionals): self
    {
        return new self(array_merge($this->items(), self::create($optionals)->somes()->items()));
    }

    /**
     * @param Optional<T> ...$optionals
     * @return  Optionals<T>
     */
    public function mergeFailures(Optional ...$optionals): self
    {
        return new self(array_merge($this->items(), self::create($optionals)->failures()->items()));
    }

    public function count(): int
    {
        return count($this->items());
    }

    /**
     * @param T $default
     * @return Optional<T>
     */
    public function head($default = null): Optional
    {
        return first($this->items()) ?? $default ?? None::create();
    }

    /** @return self<T> */
    public function tail(): self
    {
        return self::create(tail($this->items()));
    }
}
