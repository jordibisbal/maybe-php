<?php

namespace j45l\either;

class Tag
{
    public static function from(string $tag): Tag
    {
        /** @noinspection DegradedSwitchInspection */
        /** @infection-ignore-all */
        switch (true) {
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
