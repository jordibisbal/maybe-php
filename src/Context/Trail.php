<?php

declare(strict_types=1);

namespace j45l\either\Context;

use Closure;
use Countable;
use j45l\either\Either;
use j45l\either\Failure;
use j45l\either\Reason;
use j45l\either\Some;
use j45l\either\Tags\StringTag;
use j45l\either\Tags\Tag;
use j45l\either\Tags\Untagged;
use function Functional\invoke;
use function Functional\map;
use function Functional\select;

final class Trail implements Countable
{
    /** @var array<Either> */
    protected $trail = [];

    /** @var array<Either> */
    private $taggedTrail = [];

    private function __construct()
    {
    }

    public static function create(): Trail
    {
        return new self();
    }

    public function push(Either $either, Tag $tag = null): Trail
    {
        $new = clone $this;
        $new->trail[] = $either;
        $new->taggedTrail = $this->pushWithTag($either, $tag ?? Untagged::create());

        return $new;
    }

    /** @return Either[] */
    public function asArray(): array
    {
        return $this->trail;
    }

    /** @return Failure[] */
    public function failed(): array
    {
        return array_values(
            select(
                $this->trail,
                function (Either $either) {
                    return $either instanceof Failure;
                }
            )
        );
    }

    /** @return array<Either> */
    public function getValues(): array
    {
        return array_values(map($this->selectSome($this->trail), $this->pickValue()));
    }

    /**
     * @param array<Either> $items
     * @return array<Some>
     */
    private function selectSome(array $items): array
    {
        return select($items, function ($item) {
            return $item instanceof Some;
        });
    }

    /**
     * @param array<Either> $items
     * @return array<Some>
     */
    private function selectFailures(array $items): array
    {
        return select($items, function ($item) {
            return $item instanceof Failure;
        });
    }

    private function pickValue(): Closure
    {
        return static function (Some $some) {
            return $some->value();
        };
    }

    public function empty(): bool
    {
        /** @infection-ignore-all */
        return $this->count() === 0;
    }

    public function count()
    {
        return count($this->trail);
    }

    public function butLast(): Trail
    {
        $new = clone $this;
        $new->trail = array_slice($new->trail, 0, count($new->trail) - 1);

        return $new;
    }

    public function justLast(): Trail
    {
        $new = self::create();
        /** @infection-ignore-all */
        $new->trail = array_slice($this->trail, -1, 1);

        return $new;
    }

    /** @return array<Either> */
    private function pushWithTag(Either $either, Tag $tag): array
    {
        /** @noinspection DegradedSwitchInspection */
        /** @infection-ignore-all */
        switch (true) {
            case $tag instanceof StringTag:
                return array_replace($this->taggedTrail, [$tag->asString() => $either]);
            default:
                return $this->taggedTrail;
        }
    }

    /** @return array<string, Some> */
    public function getTaggedValues(): array
    {
        return invoke($this->selectSome($this->taggedTrail), 'value');
    }

    /** @return array<string, Reason> */
    public function getTaggedFailureReasons(): array
    {
        return invoke($this->selectFailures($this->taggedTrail), 'reason');
    }

    /** @return array<string, Either> */
    public function getTagged(): array
    {
        return $this->taggedTrail;
    }
}
