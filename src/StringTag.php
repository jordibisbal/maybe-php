<?php

namespace j45l\either;

class StringTag extends Tag
{
    /** @var string */
    private $tag;

    protected function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public function asString(): string
    {
        return $this->tag;
    }
}
