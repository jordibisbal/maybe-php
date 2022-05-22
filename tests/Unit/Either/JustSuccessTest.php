<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either;

use j45l\maybe\Either\JustSuccess;
use PHPUnit\Framework\TestCase;

/**
 * @covers \j45l\maybe\Either\JustSuccess
 */
final class JustSuccessTest extends TestCase
{
    public function testCanBeMappedToItself(): void
    {
        $success = JustSuccess::create();
        $mappedSuccess = $success->map(function () {
            return null;
        });

        $this->assertSame($success, $mappedSuccess);
    }
}
