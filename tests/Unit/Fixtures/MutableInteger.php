<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Fixtures;

final class MutableInteger
{
    /** @var int */
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function change(int $value): void
    {
        $this->value = $value;
    }

    public function asInt(): int
    {
        return $this->value;
    }
}
