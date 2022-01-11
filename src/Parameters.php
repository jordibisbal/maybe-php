<?php

declare(strict_types=1);

namespace j45l\either;

final class Parameters
{
    private $parameters;

    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function create(...$parameters): Parameters
    {
        return new self($parameters);
    }

    public function asArray(): array
    {
        return $this->parameters;
    }
}
