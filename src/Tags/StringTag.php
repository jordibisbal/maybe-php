<?php

namespace j45l\maybe\Tags;

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

    public function toString(): string
    {
        return $this->tag;
    }
}
