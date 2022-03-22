<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\maybe\Maybe;
use j45l\maybe\None;
use j45l\maybe\Some;
use RuntimeException;

/**
 * @template T
 */
class EntityManagerStub
{
    /** @var Maybe<T> */
    public $insertInvokedWith;
    /** @var Maybe<T> */
    public $updateInvokedWith;

    /** @var bool */
    public $insertWillFail;
    /** @var bool */
    public $updateWillFail;

    public function __construct()
    {
        $this->insertInvokedWith = None::create();
        $this->updateInvokedWith = None::create();

        $this->insertWillFail = false;
        $this->updateWillFail = false;
    }

    /**
     * @param Some<T> $some
     */
    public function insert(Some $some): void
    {
        $this->insertInvokedWith = $some;

        if ($this->insertWillFail) {
            throw new RuntimeException('Failed to insert');
        }
    }

    /**
     * @param Some<T> $some
     */
    public function update(Some $some): void
    {
        $this->updateInvokedWith = $some;

        if ($this->updateWillFail) {
            throw new RuntimeException('Failed to update');
        }
    }
}
