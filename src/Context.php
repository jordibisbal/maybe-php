<?php

declare(strict_types=1);

namespace j45l\either;

final class Context
{
    /** @var Parameters */
    private $parameters;
    /** @var Trail */
    private $trail;
    /** @var Tag */
    private $tag;

    private function __construct(Parameters $parameters, Trail $trail, Tag $tag)
    {
        $this->parameters = $parameters;
        $this->trail = $trail;
        $this->tag = $tag;
    }

    public static function create(): Context
    {
        return new self(Parameters::create(), Trail::create(), Tag::untagged());
    }

    public static function fromParameters(Parameters $parameters): Context
    {
        return new self($parameters, Trail::create(), Tag::untagged());
    }

    /** @param array<mixed> $parameters */
    public function withParameters(...$parameters): Context
    {
        return new self(Parameters::create(...$parameters), $this->trail(), $this->tag);
    }

    public function withTag(Tag $tag): Context
    {
        return new self($this->parameters, $this->trail(), $tag);
    }

    public function trail(): Trail
    {
        return $this->trail;
    }

    public function push(Either $either): Context
    {
        return new self($this->parameters(), $this->trail()->push($either, $this->tag), $this->tag);
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }
}
