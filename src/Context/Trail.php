<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use Countable;
use j45l\maybe\Maybe;

/**
 * @template T
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class Trail implements Countable
{
    /** @phpstan-use MaybeAware<T> */
    use MaybeAware;

    private function __construct()
    {
    }

    /** @return Trail<T> */
    public static function create(): Trail
    {
        return new self();
    }

    /**
     * @param Maybe<T> $maybe
     * @return Trail<T>
     */
    public function push(Maybe $maybe): Trail
    {
        $new = clone $this;
        $new->maybes[] = $maybe;

        return $new;
    }

    /** @return Maybe<T>[] */
    public function asArray(): array
    {
        return $this->maybes;
    }

    public function empty(): bool
    {
        /** @infection-ignore-all */
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->maybes);
    }

    /**
     * @return Trail<T>
     */
    public function butLast(): Trail
    {
        $new = clone $this;
        $new->maybes = array_slice($new->maybes, 0, count($new->maybes) - 1);

        return $new;
    }

    /**
     * @return Trail<T>
     */
    public function last(): Trail
    {
        $new = self::create();
        /** @infection-ignore-all */
        $new->maybes = array_slice($this->maybes, -1, 1);

        return $new;
    }
}
