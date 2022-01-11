<?php

declare(strict_types=1);

namespace j45l\either;

final class Context
{
    private $parameters;
    private $trail;

    private function __construct(Parameters $parameters, Trail $trail)
    {
        $this->parameters = $parameters;
        $this->trail = $trail;
    }

    public static function create(): Context
    {
        return new self(Parameters::create(), Trail::create());
    }

    public static function fromParameters(Parameters $parameters): Context
    {
        return new self($parameters, Trail::create());
    }

    public function withParameters(...$parameters): Context
    {
        return new self(Parameters::create(...$parameters), $this->trail());
    }

    public function trail(): Trail
    {
        return $this->trail;
    }

    public function push(Either $either): Context
    {
        return new self($this->parameters(), $this->trail()->push($either));
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }
}
