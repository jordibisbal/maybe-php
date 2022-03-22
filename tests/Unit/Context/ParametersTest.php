<?php

namespace j45l\maybe\Test\Unit\Context;

use j45l\maybe\Context\Parameters;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/** @covers \j45l\maybe\Context\Parameters */
class ParametersTest extends TestCase
{
    public function testParametersCanBeRetrievedAsArray(): void
    {
        assertEquals([1, 2, 3], Parameters::create(1, 2, 3)->asArray());
    }
}
