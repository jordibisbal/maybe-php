<?php

namespace j45l\either\Test\Unit;

use j45l\either\Parameters;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

/** @covers \j45l\either\Parameters */
class ParametersTest extends TestCase
{
    public function testParametersCanBeRetrievedAsArray(): void
    {
        assertEquals([1, 2, 3], Parameters::create(1, 2, 3)->asArray());
    }
}
