<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either;

use j45l\maybe\Either\Reason;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\PhpUnit\assertNone;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

/** @covers \j45l\maybe\Either\Reason */
final class ReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $reason = Reason::fromString('reason');

        assertEquals('reason', $reason->toString());
        assertNone($reason->subject());
    }

    public function testCanBeCreatedFromAFormat(): void
    {
        $reason = Reason::fromFormatted('because %s and %s', 'one', 'another');

        assertEquals('because one and another', $reason->toString());
        assertNone($reason->subject());
    }

    public function testReasonStringCanBeChangedFromAFormat(): void
    {
        $reason = Reason::fromString('Original One');
        $newReason = $reason->withFormatted('because %s and %s', 'one', 'another');

        assertEquals('because one and another', $newReason->toString());
        assertEquals('Original One', $reason->toString());
        assertNone($reason->subject());
        assertNotSame($reason, $newReason);
    }

    public function testReasonCanBeCastToString(): void
    {
        assertEquals('an string', (string)(Reason::fromString('an string')));
    }

    public function testAReasonCanHaveASubject(): void
    {
        $subject = Some::from(42);
        $reason = Reason::fromString('')->withSubject($subject);

        assertSame($subject, $reason->subject());
    }

    public function testWithSubjectIsNotMutagen(): void
    {
        $subject = Some::from(42);
        $reason = Reason::fromString('');
        $newReason = $reason->withSubject($subject);

        assertNotSame($reason, $newReason);
        assertSame($subject, $newReason->subject());
    }
}
