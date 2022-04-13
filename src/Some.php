<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\maybe\Context\Context;

/**
 * @template T
 * @extends Maybe<T>
 */
class Some extends Maybe
{
    /** @var mixed */
    private $value;

    /**
     * @param T $value
     * @param Context<T> $context
     */
    protected function __construct($value, Context $context)
    {
        parent::__construct($context);

        $this->value = $value;
    }

    /**
     * @param mixed $value
     * @return Some<T>
     */
    public static function from($value): Some
    {
        return new self($value, Context::create());
    }

    /** @return T $value */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return T
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrElse($value)
    {
        return $this->get();
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
