<?php

declare(strict_types=1);

namespace j45l\maybe;

use j45l\maybe\Context\Context;

use function j45l\functional\take;

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

    /** @return T */
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

    /**
     * @param mixed $default
     * @param string|int|array<string|int> $propertyName
     * @return Some<mixed>
     */
    public function takeOrElse($propertyName, $default): Some
    {
        return Some::from(take($this->get(), $propertyName, $default));
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
