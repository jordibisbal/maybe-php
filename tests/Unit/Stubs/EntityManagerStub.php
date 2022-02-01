<?php

namespace j45l\either\Test\Unit\Stubs;

use j45l\either\Either;
use j45l\either\None;
use j45l\either\Some;
use RuntimeException;

class EntityManagerStub
{
    /** @var Either */
    public $insertInvokedWith;
    /** @var Either */
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

    public function insert(Some $some): void
    {
        $this->insertInvokedWith = $some;

        if ($this->insertWillFail) {
            throw new RuntimeException('Failed to insert');
        }
    }

    public function update(Some $some): void
    {
        $this->updateInvokedWith = $some;

        if ($this->updateWillFail) {
            throw new RuntimeException('Failed to update');
        }
    }
}
