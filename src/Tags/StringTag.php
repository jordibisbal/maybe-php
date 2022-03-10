<?php

namespace j45l\either\Tags;

final class StringTag implements Tag
{
    /** @var string */
    private $tag;

    private function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public static function create(string $tag): StringTag
    {
        return new self($tag);
    }

    public function asString(): string
    {
        return $this->tag;
    }
}