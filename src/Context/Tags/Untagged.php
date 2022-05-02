<?php

namespace j45l\maybe\Context\Tags;

final class Untagged implements Tag
{
    public static function create(): Untagged
    {
        return new self();
    }
}
