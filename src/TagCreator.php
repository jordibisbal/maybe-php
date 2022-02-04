<?php

namespace j45l\either;

use j45l\either\Tags\StringTag;
use j45l\either\Tags\Tag;
use j45l\either\Tags\Untagged;

class TagCreator
{
    /** @param Tag | string | int $tag */
    public static function from($tag): Tag
    {
        /** @infection-ignore-all */
        switch (true) {
            case $tag instanceof Tag:
                return $tag;
            /** @infection-ignore-all */
            case is_int($tag)
                || (is_string($tag) && trim($tag) !== ''):
                return StringTag::create((string) $tag);
            default:
                return new Untagged();
        }
    }
}
