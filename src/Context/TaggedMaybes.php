<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use j45l\maybe\Context\Tags\StringTag;
use j45l\maybe\Context\Tags\Tag;
use j45l\maybe\Context\Tags\Untagged;
use j45l\maybe\Maybe;

/**
 * @template T
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
final class TaggedMaybes
{
    /** @phpstan-use MaybeAware<T> */
    use MaybeAware;

    /** @var Tag */
    private $activeTag;

    private function __construct()
    {
        $this->activeTag = Untagged::create();
    }

    /** @return TaggedMaybes<T> */
    public static function create(): TaggedMaybes
    {
        return new self();
    }

    /** @return TaggedMaybes<T> */
    public function withTag(Tag $tag): TaggedMaybes
    {
        $new = clone $this;
        $new->activeTag = $tag;

        return $new;
    }

    public function active(): bool
    {
        return !$this->activeTag instanceof Untagged;
    }

    /**
     * @param Maybe<T> $maybe
     * @return TaggedMaybes<T>
     */
    public function set(Maybe $maybe): TaggedMaybes
    {
        $tag = $this->activeTag;
        if (!$tag instanceof StringTag) {
            return $this;
        }

        $new = clone $this;
        $new->maybes[$tag->toString()] = $maybe;

        return $new;
    }

    public function activeTag(): Tag
    {
        return $this->activeTag;
    }
}
