<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

final class Parameters
{
    /** @var array<mixed>  */
    private $parameters;

    /** @param array<mixed> $parameters */
    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /** @param array<mixed> $parameters */
    public static function create(...$parameters): Parameters
    {
        return new self($parameters);
    }

    /** @return array<mixed> $parameters */
    public function asArray(): array
    {
        return $this->parameters;
    }
}
