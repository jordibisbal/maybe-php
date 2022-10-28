<?php

namespace j45l\maybe\Test\Unit\Fixtures;

use j45l\maybe\Optional\Optional;
use j45l\maybe\Maybe\None;
use j45l\maybe\Maybe\Some;
use RuntimeException;

/**
 * @template T
 */
class EntityManagerStub
{
    /** @var Optional<T> */
    public Optional $insertInvokedWith;
    /** @var Optional<T> */
    public Optional $updateInvokedWith;

    public bool $insertWillFail;
    public bool $updateWillFail;

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
