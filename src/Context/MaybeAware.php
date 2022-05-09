<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use j45l\maybe\DoTry\Failure;
use j45l\maybe\DoTry\Reason;
use j45l\maybe\Maybe;
use j45l\maybe\Some;

use function Functional\map;
use function Functional\select;

/** @template T */
trait MaybeAware
{
    /**
     * @var array<Maybe<T>>
     */
    private $maybes = [];

    /** @return Failure<T>[] */
    public function failed(): array
    {
        return array_values(
            $this->failures()
        );
    }

    /** @return mixed[] */
    public function someValues(): array
    {
        return map($this->some(), $this->pickValue());
    }

    /** @return array<Reason> */
    public function failureReasons(): array
    {
        return map($this->failures(), $this->pickReason());
    }

    /** @return array<string> */
    public function failureReasonStrings(): array
    {
        return map($this->failures(), $this->pickReasonString());
    }

    /**
     * @return array<Some<T>>
     */
    private function some(): array
    {
        return select($this->maybes, function ($item) {
            return $item instanceof Some;
        });
    }

    /**
     * @return array<Failure<T>>
     */
    private function failures(): array
    {
        return select($this->maybes, function ($item) {
            return $item instanceof Failure;
        });
    }

    private function pickValue(): callable
    {
        return static function (Some $some) {
            return $some->get();
        };
    }

    private function pickReasonString(): callable
    {
        return static function (Failure $some) {
            return $some->reason()->toString();
        };
    }

    private function pickReason(): callable
    {
        return static function (Failure $some) {
            return $some->reason();
        };
    }
}
