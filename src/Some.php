<?php

declare(strict_types=1);

namespace j45l\either;

use j45l\either\Context\Context;

/**
 * @template T
 * @extends Either<T>
 */
class Some extends Either
{
    /** @var mixed */
    private $value;

    /** @param mixed $value */
    protected function __construct($value, Context $context)
    {
        parent::__construct($context);

        $this->value = $value;
    }

    /** @param mixed $value */
    public static function from($value): Some
    {
        return new self($value, Context::create());
    }

    /** @return mixed $value */
    public function value()
    {
        return $this->value;
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
