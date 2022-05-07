<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MattHarvey\CivilDateTime\CivilDate;

final class CivilDateTest extends TestCase
{
    public function testFromIsoDateStampAndToIsoDateStamp(): void
    {
        $this->assertSame(null, CivilDate::fromIsoDateStamp('1234'));
        $this->assertSame(null, CivilDate::fromIsoDateStamp('2019-02-29'));
        $this->assertSame(null, CivilDate::fromIsoDateStamp('2019-02-25Z'));
        $this->assertSame(null, CivilDate::fromIsoDateStamp('2019-02-25+1100'));
        $this->assertSame(null, CivilDate::fromIsoDateStamp('2019-2-35T01:01'));
        $this->assertInstanceOf(CivilDate::class, CivilDate::fromIsoDateStamp('2010-10-15'));
        $this->assertSame('2010-10-15', CivilDate::fromIsoDateStamp('2010-10-15')->toIsoDateStamp());
        $this->assertInstanceOf(CivilDate::class, CivilDate::fromIsoDateStamp('2010-06-30'));
        $this->assertSame('2010-06-30', CivilDate::fromIsoDateStamp('2010-06-30')->toIsoDateStamp());
        $this->assertSame('300-12-01', CivilDate::fromIsoDateStamp('300-12-01')->toIsoDateStamp());
        $this->assertSame('3000-12-01', CivilDate::fromIsoDateStamp('3000-12-01')->toIsoDateStamp());
    }

    public function testForMomentInTimezone(): void
    {
        $sydney = new DateTimeZone('Australia/Sydney');
        $adelaide = new DateTimeZone('Australia/Adelaide');

        $dateTime = new DateTimeImmutable();
        $utc = new DateTimeZone('UTC');
        $dateTime = $dateTime->setDate(2021, 1, 22)->setTime(19, 55, 0)->setTimezone($utc);
        $this->assertSame('2021-01-23', CivilDate::forMomentInTimezone($dateTime, $sydney)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable();
        $utc = new DateTimeZone('UTC');
        $dateTime = $dateTime->setDate(2021, 1, 22)->setTime(20, 19, 16)->setTimezone($utc);
        $this->assertSame('2021-01-23', CivilDate::forMomentInTimezone($dateTime, $adelaide)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-22 20:19:16+0');
        $this->assertSame('2021-01-23', CivilDate::forMomentInTimezone($dateTime, $adelaide)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-22 13:30:00+0');
        $this->assertSame('2021-01-23', CivilDate::forMomentInTimezone($dateTime, $adelaide)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-22 13:29:59+0');
        $this->assertSame('2021-01-22', CivilDate::forMomentInTimezone($dateTime, $adelaide)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-22 13:30:01+0');
        $this->assertSame('2021-01-23', CivilDate::forMomentInTimezone($dateTime, $adelaide)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-26 20:34:00+11');
        $this->assertSame('2021-01-26', CivilDate::forMomentInTimezone($dateTime, $sydney)->toIsoDateStamp());

        $dateTime = new DateTimeImmutable('2021-01-26 09:40:00+0');
        $this->assertSame('2021-01-26', CivilDate::forMomentInTimezone($dateTime, $sydney)->toIsoDateStamp());
    }

    public function testFormat(): void
    {
        $date = CivilDate::fromIsoDateStamp('2021-04-24');
        $this->assertSame('Sat 24 Apr 2021', $date->format('D j M Y'));
        $this->assertSame('Saturday 24 April 2021', $date->format('l j F Y'));
    }

    public function testAdd(): void
    {
        $this->assertSame(
            '2010-03-08',
            CivilDate::fromIsoDateStamp('2010-03-05')->addDays(3)->toIsoDateStamp(),
        );
        $this->assertSame(
            '2010-03-02',
            CivilDate::fromIsoDateStamp('2010-03-05')->addDays(-3)->toIsoDateStamp(),
        );
        $this->assertSame(
            '2010-03-05',
            CivilDate::fromIsoDateStamp('2010-03-05')->addDays(0)->toIsoDateStamp(),
        );
        $this->assertSame(
            '2008-12-29',
            CivilDate::fromIsoDateStamp('2009-01-03')->addDays(-5)->toIsoDateStamp(),
        );
        $this->assertSame(
            '2010-05-08',
            CivilDate::fromIsoDateStamp('2010-04-28')->addDays(10)->toIsoDateStamp(),
        );
    }

    /** @suppress PhanTypeMismatchArgumentNullable */
    public function testDiffDays(): void
    {
        $dayA = CivilDate::fromIsoDateStamp('2010-03-05');
        $dayB = CivilDate::fromIsoDateStamp('2010-04-05');
        $dayC = CivilDate::fromIsoDateStamp('2010-03-05');
        $dayD = CivilDate::fromIsoDateStamp('2010-03-06');
        $dayE = CivilDate::fromIsoDateStamp('2011-03-06');
        $dayF = CivilDate::fromIsoDateStamp('1996-12-18');
        $dayG = CivilDate::fromIsoDateStamp('1995-12-18');

        $this->assertSame(31, CivilDate::diffDays($dayB, $dayA));
        $this->assertSame(-31, CivilDate::diffDays($dayA, $dayB));
        $this->assertSame(-1, CivilDate::diffDays($dayC, $dayD));
        $this->assertSame(0, CivilDate::diffDays($dayD, $dayD));
        $this->assertSame(0, CivilDate::diffDays($dayA, $dayC));
        $this->assertSame(365, CivilDate::diffDays($dayE, $dayD));
        $this->assertSame(-366, CivilDate::diffDays($dayG, $dayF));
    }

    /** @suppress PhanTypeMismatchArgumentNullable */
    public function testLaterThan(): void
    {
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-05')->laterThan(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-06')->laterThan(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertTrue(
            CivilDate::fromIsoDateStamp('2010-03-07')->laterThan(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-02-07')->laterThan(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2009-05-10')->laterThan(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertTrue(
            CivilDate::fromIsoDateStamp('2010-03-06')->laterThan(CivilDate::fromIsoDateStamp('2009-05-10'))
        );
        $this->assertTrue(
            CivilDate::fromIsoDateStamp('2010-03-06')->laterThan(CivilDate::fromIsoDateStamp('2010-03-05'))
        );
    }

    /** @suppress PhanTypeMismatchArgumentNullable */
    public function testEquals(): void
    {
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-05')->equals(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertTrue(
            CivilDate::fromIsoDateStamp('2010-03-06')->equals(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-07')->equals(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-02-07')->equals(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2009-05-10')->equals(CivilDate::fromIsoDateStamp('2010-03-06'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-06')->equals(CivilDate::fromIsoDateStamp('2009-05-10'))
        );
        $this->assertFalse(
            CivilDate::fromIsoDateStamp('2010-03-06')->equals(CivilDate::fromIsoDateStamp('2010-03-05'))
        );
        $this->assertTrue(
            CivilDate::fromIsoDateStamp('1996-02-29')->equals(CivilDate::fromIsoDateStamp('1996-02-29'))
        );
    }

    /** @suppress PhanPluginRemoveDebugEcho, PhanTypeSuspiciousEcho */
    public function testToString(): void
    {
        $this->assertSame('2019-09-03', CivilDate::fromIsoDateStamp('2019-09-03')?->__toString());
        ob_start();
        echo CivilDate::fromIsoDateStamp('1993-05-16');
        $str = ob_get_contents();
        ob_end_clean();
        $this->assertSame('1993-05-16', $str);
    }
}
