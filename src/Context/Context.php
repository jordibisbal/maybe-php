<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use j45l\maybe\Context\Tags\StringTag;
use j45l\maybe\Maybe;

/**
 * @template T
 */
final class Context
{
    /** @var Parameters */
    private $parameters;
    /** @var Trail<T> */
    private $trail;
    /**
     * @var TaggedMaybes<T>
     */
    private $tags;

    /**
     * @param Trail<T> $trail
     * @param TaggedMaybes<T> $tags
     */
    private function __construct(Parameters $parameters, Trail $trail, TaggedMaybes $tags)
    {
        $this->parameters = $parameters;
        $this->trail = $trail;
        $this->tags = $tags;
    }

    /** @return Context<T> */
    public static function create(): Context
    {
        return new self(Parameters::create(), Trail::create(), TaggedMaybes::create());
    }

    /** @return Context<T> */
    public static function fromParameters(Parameters $parameters): Context
    {
        return new self($parameters, Trail::create(), TaggedMaybes::create());
    }

    /**
     * @param array<mixed> $parameters
     * @return Context<T>
     */
    public function withParameters(...$parameters): Context
    {
        return new self(Parameters::create(...$parameters), $this->trail(), $this->tags);
    }

    /**
     * @return Context<T>
     */
    public function withTag(StringTag $tag): Context
    {
        return new self($this->parameters, $this->trail(), $this->tags->withTag($tag));
    }

    /** @return Trail<T> */
    public function trail(): Trail
    {
        return $this->trail;
    }

    /**
     * @param Maybe<T> $maybe
     * @return Context<T>
     */
    public function push(Maybe $maybe): Context
    {
        return new self($this->parameters(), $this->trail()->push($maybe), $this->tags);
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }

    /**
     * @param Maybe<T> $maybe
     * @return Context<T>
     */
    public function tag(Maybe $maybe): Context
    {
        return new self($this->parameters(), $this->trail(), $this->tags->set($maybe));
    }

    /**
     * @return TaggedMaybes<T>
     */
    public function tagged(): TaggedMaybes
    {
        return $this->tags;
    }
}
