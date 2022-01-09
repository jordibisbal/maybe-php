<?php

declare(strict_types=1);

namespace j45l\either;

use Closure;
use Countable;
use function Functional\map;
use function Functional\select;

final class Trail implements Countable
{
    protected $trail = [];

    private function __construct()
    {
    }

    public static function create(): Trail
    {
        return new self();
    }

    public function push(Either $either): Trail
    {
        $new = clone $this;
        $new->trail[] = $either;

        return $new;
    }

    /** @return Either[] */
    public function asArray(): array
    {
        return $this->trail;
    }

    /** @return Failed[] */
    public function failed(): array
    {
        return array_values(
            select(
                $this->trail,
                function (Either $either) {
                    return $either instanceof Failed;
                }
            )
        );
    }

    public function getValues(): array
    {
        return array_values(map($this->selectSome($this->trail), $this->pickValue()));
    }

    /** @return Some[] */
    private function selectSome($items): array
    {
        return select($items, function ($item) {
            return $item instanceof Some;
        });
    }

    private function pickValue(): Closure
    {
        return static function (Some $some) {
            return $some->value();
        };
    }

    public function isEmpty(): bool
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
}
