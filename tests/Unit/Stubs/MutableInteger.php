<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit\Stubs;

final class MutableInteger
{
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
