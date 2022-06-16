<?php

namespace j45l\maybe\Test\Unit\Optional;

use j45l\maybe\Either\Failure;
use j45l\maybe\Either\JustSuccess;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function j45l\maybe\Optional\guard;

/** @covers ::j45l\maybe\Optional\guard */
class GuardTest extends TestCase
{
    public function testAFailingGuardWithoutThrowableReturnsFailure(): void
    {
        $this->assertInstanceOf(Failure::class, guard(false));
    }

    public function testAFailingGuardWitThrowableThrows(): void
    {
        $this->expectExceptionObject(new RuntimeException('Failed guard'));

        guard(false, 'Failed guard');
    }

    public function testAPassingGuardReturnsJustSuccess(): void
    {
        $this->assertInstanceOf(JustSuccess::class, guard(true));
    }
}