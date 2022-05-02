<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use j45l\maybe\Context\Tags\Tag;
use j45l\maybe\Context\Tags\TagCreator;

/**
 * @template T
 */
trait ContextAware
{
    /** @var Context<T> */
    protected $context;

    /**
     * @return Context<T>
     */
    public function context(): Context
    {
        return $this->context;
    }

    /**
     * @param Context<T>|null $context
     * @return static
     */
    final protected function cloneWith(Context $context = null): self
    {
        $new = clone $this;

        $new->context = $context ?? $this->context;

        return $new;
    }


    /**
     * @param array<mixed> $parameters
     * @return static
     */
    final public function with(...$parameters): self
    {
        return $this->cloneWith($this->context->withParameters(...$parameters));
    }

    /**
     * @param mixed[] $parameters
     * @return static
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function withParameters(...$parameters): self
    {
        /** @infection-ignore-all */
        switch (true) {
            case count($parameters) > 0:
                return $this->with(...$parameters);
            default:
                return $this;
        }
    }

    /**
     * @param Tag | int | string $tag
     * @return self<T>
     */
    final public function withTag($tag): self
    {
        return $this->resolve()->cloneWith(
            $this->context()->push($this->resolve())
                ->withTag(TagCreator::from($tag))
        );
    }

    /**
     * @return Trail<T>
     */
    public function trail(): Trail
    {
        return $this->context->push($this->resolve())->trail();
    }
}
