<?php

declare(strict_types=1);

namespace j45l\either\Context;

use j45l\either\Either;
use j45l\either\Tags\Tag;
use j45l\either\Tags\Untagged;

/**
 * @template T
 */
final class Context
{
    /** @var Parameters */
    private $parameters;
    /** @var Trail<T> */
    private $trail;
    /** @var Tag */
    private $tag;

    /** @param Trail<T> $trail */
    private function __construct(Parameters $parameters, Trail $trail, Tag $tag)
    {
        $this->parameters = $parameters;
        $this->trail = $trail;
        $this->tag = $tag;
    }

    /** @return Context<T> */
    public static function create(): Context
    {
        return new self(Parameters::create(), Trail::create(), new Untagged());
    }

    /** @return Context<T> */
    public static function fromParameters(Parameters $parameters): Context
    {
        return new self($parameters, Trail::create(), new Untagged());
    }

    /**
     * @param array<mixed> $parameters
     * @return Context<T>
     */
    public function withParameters(...$parameters): Context
    {
        return new self(Parameters::create(...$parameters), $this->trail(), $this->tag);
    }

    /** @return Context<T> */
    public function withTag(Tag $tag): Context
    {
        return new self($this->parameters, $this->trail(), $tag);
    }

    /** @return Trail<T> */
    public function trail(): Trail
    {
        return $this->trail;
    }

    /**
     * @param Either<T> $either
     * @return Context<T>
     */
    public function push(Either $either): Context
    {
        return new self($this->parameters(), $this->trail()->push($either, $this->tag), $this->tag);
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }

    public function tag(): Tag
    {
        return $this->tag;
    }
}
