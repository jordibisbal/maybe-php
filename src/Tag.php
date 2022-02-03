<?php

namespace j45l\either;

class Tag
{
    private function __construct()
    {
    }

    /** @param Tag | string $tag */
    public static function from($tag): Tag
    {
        /** @infection-ignore-all */
        switch (true) {
            case $tag instanceof self:
                return $tag;
            /** @infection-ignore-all */
            case (trim($tag) !== ''):
                return new StringTag($tag);
            default:
                return self::untagged();
        }
    }

    public static function untagged(): Untagged
    {
        return new Untagged();
    }
}
