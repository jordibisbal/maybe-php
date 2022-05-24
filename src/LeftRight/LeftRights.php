<?php

declare(strict_types=1);

namespace j45l\maybe\LeftRight;

use j45l\maybe\Either\Failure;
use j45l\maybe\Maybe\Some;

use function Functional\map;
use function Functional\select;

/**
 * @template T
 */
final class LeftRights
{
    /**
     * @var LeftRight<T>[]
     */
    private $leftRights;

    /** @phpstan-param LeftRight<T>[] $leftRights */
    private function __construct(array $leftRights)
    {
        $this->leftRights = $leftRights;
    }

    /**
     * @param LeftRight<T>[] $leftRights
     * @return self<T>
     */
    public static function create(array $leftRights): self
    {
        return new self($leftRights);
    }

    /** @return self<T> */
    public function somes(): self
    {
        return self::create(select(
            $this->leftRights,
            function (LeftRight $leftRight) {
                return isSome($leftRight);
            }
        ));
    }

    /** @return self<T> */
    public function successes(): self
    {
        return self::create(select(
            $this->leftRights,
            function (LeftRight $leftRight) {
                return isSuccess($leftRight);
            }
        ));
    }

    /** @return self<T> */
    public function nones(): self
    {
        return self::create(select(
            $this->leftRights,
            function (LeftRight $leftRight) {
                return isNone($leftRight);
            }
        ));
    }

    /** @return self<T> */
    public function failures(): self
    {
        return self::create(select(
            $this->leftRights,
            function (LeftRight $leftRight) {
                return isFailure($leftRight);
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

    /** @return LeftRight<T>[] */
    public function items(): array
    {
        return $this->leftRights;
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
}
