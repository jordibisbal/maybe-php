<?php

namespace j45l\maybe\Test\Unit\Optional;

use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\guard;
use function j45l\maybe\Optional\PhpUnit\assertFailure;
use function j45l\maybe\Optional\PhpUnit\assertJustSuccess;

/** @covers ::j45l\maybe\Optional\guard */
class GuardTest extends TestCase
{
    public function testAFailingGuardWithoutThrowableReturnsFailure(): void
    {
        assertFailure(guard(false));
    }

    public function testAFailingGuardWitThrowableThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException('Failed guard'));

        guard(false, 'Failed guard');
    }

    public function testAPassingGuardReturnsJustSuccess(): void
    {
        assertJustSuccess(guard(true));
    }
}
