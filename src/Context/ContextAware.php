<?php

declare(strict_types=1);

namespace j45l\maybe\Context;

use j45l\maybe\Context\Tags\StringTag;

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
    final public function context(): Context
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
    final protected function withParameters(...$parameters): self
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
     * @param string $tag
     * @return self<T>
     */
    final public function tag(string $tag): self
    {
        return $this->cloneWith($this->context()->withTag(StringTag::create($tag)));
    }

    /**
     * @return Trail<T>
     */
    final public function trail(): Trail
    {
        return $this->context->push($this->resolve())->trail();
    }

    /**
     * @return Context<T>
     */
    final protected function track(): Context
    {
        return $this->context()->push($this);
    }
}
