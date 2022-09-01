<?php

declare(strict_types=1);

namespace j45l\maybe\Test\Unit\Either\Reasons;

use j45l\maybe\Either\Reasons\FailureReason;
use j45l\maybe\Maybe\Some;
use PHPUnit\Framework\TestCase;

use function j45l\maybe\Optional\PhpUnit\assertNone;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

/** @covers \j45l\maybe\Either\Reasons\FailureReason */
final class ReasonTest extends TestCase
{
    public function testCanBeCreatedFromAnString(): void
    {
        $reason = FailureReason::fromString('reason');

        assertEquals('reason', $reason->toString());
        assertNone($reason->subject());
    }

    public function testCanBeCreatedFromAFormat(): void
    {
        $reason = FailureReason::fromFormatted('because %s and %s', 'one', 'another');

        assertEquals('because one and another', $reason->toString());
        assertNone($reason->subject());
    }

    public function testReasonCanBeCastToString(): void
    {
        assertEquals('an string', (string)(FailureReason::fromString('an string')));
    }

    public function testAReasonCanHaveASubject(): void
    {
        $subject = Some::from(42);
        $reason = FailureReason::fromString('')->withSubject($subject);

        assertSame($subject, $reason->subject());
    }

    public function testWithSubjectIsNotMutagen(): void
    {
        $subject = Some::from(42);
        $reason = FailureReason::fromString('');
        $newReason = $reason->withSubject($subject);

        assertNotSame($reason, $newReason);
        assertSame($subject, $newReason->subject());
    }
}
