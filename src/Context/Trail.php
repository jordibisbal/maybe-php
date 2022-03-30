<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use Countable;
use j45l\maybe\Maybe;
use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Reason;
use j45l\maybe\Some;
use j45l\maybe\Tags\StringTag;
use j45l\maybe\Tags\Tag;
use j45l\maybe\Tags\Untagged;

use function Functional\invoke;
use function Functional\map;
use function Functional\select;
use function j45l\functional\unindex;

/**
 * @template T
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class Trail implements Countable
{
    /** @var array<Maybe<T>> */
    protected $trail = [];

    /** @var array<Maybe<T>> */
    private $taggedTrail = [];

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
     * @param Tag|null $tag
     * @return Trail<T>
     */
    public function push(Maybe $maybe, Tag $tag = null): Trail
    {
        $new = clone $this;
        $new->trail[] = $maybe;
        $new->taggedTrail = $this->pushWithTag($maybe, $tag ?? Untagged::create());

        return $new;
    }

    /** @return Maybe<T>[] */
    public function asArray(): array
    {
        return $this->trail;
    }

    /** @return Failure<T>[] */
    public function failed(): array
    {
        return array_values(
            select(
                $this->trail,
                function (Maybe $maybe) {
                    return $maybe instanceof Failure;
                }
            )
        );
    }

    /** @return mixed[] */
    public function values(): array
    {
        return array_values(map($this->selectSome($this->trail), $this->pickValue()));
    }

    /**
     * @param array<Maybe<T>> $items
     * @return array<Some<T>>
     */
    private function selectSome(array $items): array
    {
        return select($items, function ($item) {
            return $item instanceof Some;
        });
    }

    /**
     * @param array<Maybe<T>> $items
     * @return array<Failure<T>>
     */
    private function selectFailures(array $items): array
    {
        return select($items, function ($item) {
            return $item instanceof Failure;
        });
    }

    private function pickValue(): callable
    {
        return static function (Some $some) {
            return $some->get();
        };
    }

    public function empty(): bool
    {
        /** @infection-ignore-all */
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->trail);
    }

    /**
     * @return Trail<T>
     */
    public function butLast(): Trail
    {
        $new = clone $this;
        $new->trail = array_slice($new->trail, 0, count($new->trail) - 1);

        return $new;
    }

    /**
     * @return Trail<T>
     */
    public function last(): Trail
    {
        $new = self::create();
        /** @infection-ignore-all */
        $new->trail = array_slice($this->trail, -1, 1);

        return $new;
    }

    /**
     * @param Maybe<T> $maybe
     * @return array<Maybe<T>>
     */
    private function pushWithTag(Maybe $maybe, Tag $tag): array
    {
        /** @noinspection DegradedSwitchInspection */
        /** @infection-ignore-all */
        switch (true) {
            case $tag instanceof StringTag:
                return array_replace($this->taggedTrail, [$tag->toString() => $maybe]);
            default:
                return $this->taggedTrail;
        }
    }

    /** @return array<string, Some<T>> */
    public function taggedValues(): array
    {
        return invoke($this->selectSome($this->taggedTrail), 'get');
    }

    /** @return array<string, Reason> */
    public function taggedFailureReasons(): array
    {
        return invoke($this->selectFailures($this->taggedTrail), 'reason');
    }

    /** @return array<Reason> */
    public function failureReasons(): array
    {
        return unindex(invoke($this->selectFailures($this->trail), 'reason'));
    }

    /** @return array<string, Maybe<T>> */
    public function tagged(): array
    {
        return $this->taggedTrail;
    }
}
