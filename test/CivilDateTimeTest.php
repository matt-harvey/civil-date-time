<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MattHarvey\CivilDateTime\CivilDate;
use MattHarvey\CivilDateTime\CivilDateTime;
use MattHarvey\CivilDateTime\CivilTime;

final class CivilDateTimeTest extends TestCase
{
    public function testFromIsoDateTimeStamp(): void
    {
        $stamp = '2010-05-06T06:13:59';
        $civilDateTime = CivilDateTime::fromIsoDateTimeStamp($stamp);
        $this->assertSame($stamp, $civilDateTime->toIsoDateTimeStamp());
    }

    public function testToIsoDateTimeStamp(): void
    {
        $civilDate = new CivilDate(2010, 5, 6);
        $civilTime = new CivilTime(6, 13, 59);
        $civilDateTime = new CivilDateTime($civilDate, $civilTime);
        $this->assertSame('2010-05-06T06:13:59', $civilDateTime->toIsoDateTimeStamp());
    }

    public function testForMomentInTimezone(): void
    {
        $sydney = new DateTimeZone('Australia/Sydney');
        $adelaide = new DateTimeZone('Australia/Adelaide');

        $dateTime = new DateTimeImmutable();
        $utc = new DateTimeZone('UTC');
        $dateTime = $dateTime->setDate(2021, 1, 22)->setTime(19, 55, 2)->setTimezone($utc);
        $this->assertSame('2021-01-23T06:55:02', CivilDateTime::forMomentInTimezone($dateTime, $sydney)->toIsoDateTimeStamp());

        $dateTime = new DateTimeImmutable();
        $utc = new DateTimeZone('UTC');
        $dateTime = $dateTime->setDate(2021, 1, 22)->setTime(20, 19, 16)->setTimezone($utc);
        $this->assertSame('2021-01-23T06:49:16', CivilDateTime::forMomentInTimezone($dateTime, $adelaide)->toIsoDateTimeStamp());

        $dateTime = new DateTimeImmutable('2021-06-26 20:34:05+10');
        $this->assertSame('2021-06-26T20:34:05', CivilDateTime::forMomentInTimezone($dateTime, $sydney)->toIsoDateTimeStamp());

        $dateTime = new DateTimeImmutable('2021-01-26 09:40:00+0');
        $this->assertSame('2021-01-26T20:40:00', CivilDateTime::forMomentInTimezone($dateTime, $sydney)->toIsoDateTimeStamp());
    }

    public function testToDateTimeImmutable(): void
    {
        $isoDateTime = 'Y-m-d\TH:i:s';

        $civilDate = new CivilDate(2010, 5, 6);
        $civilTime = new CivilTime(16, 13, 59);
        $civilDateTime = new CivilDateTime($civilDate, $civilTime);

        $sydney = new DateTimeZone('Australia/Sydney');
        $dt = $civilDateTime->toDateTimeImmutable($sydney);
        $this->assertSame('2010-05-06T06:13:59', $dt->format($isoDateTime));

        $stamp = '2020-05-06T06:10:15';
        $civilDateTime = CivilDateTime::fromIsoDateTimeStamp($stamp);
        $adelaide = new DateTimeZone('Australia/Adelaide');
        $dt = $civilDateTime->toDateTimeImmutable($adelaide);
        $this->assertSame('2020-05-05T20:40:15', $dt->format($isoDateTime));
        $this->assertSame('UTC', $dt->getTimezone()->getName());
    }

    /** @suppress PhanTypeMismatchArgumentNullable */
    public function testLaterThan(): void
    {
        $tests = [
            ['2010-05-06T20:15:13', '2010-05-06T20:15:14', false],
            ['2010-05-06T20:15:14', '2010-05-06T20:15:14', false],
            ['2010-05-06T20:15:15', '2010-05-06T20:15:14', true],
            ['2010-05-06T20:14:14', '2010-05-06T20:15:14', false],
            ['2010-05-06T20:16:14', '2010-05-06T20:15:14', true],
            ['2010-05-06T19:15:14', '2010-05-06T20:15:14', false],
            ['2010-05-06T21:15:14', '2010-05-06T20:15:14', true],
            ['2010-05-05T20:15:14', '2010-05-06T20:15:14', false],
            ['2010-05-07T20:15:14', '2010-05-06T20:15:14', true],
            ['2010-05-07T20:15:14', '2010-05-06T21:15:14', true],
            ['2010-05-05T20:15:14', '2010-05-06T20:15:14', false],
            ['2010-05-07T20:15:14', '2010-05-06T21:15:14', true],
            ['2010-06-07T20:15:14', '2010-05-08T21:15:14', true],
            ['2011-06-07T20:15:14', '2010-12-08T21:55:14', true],
            ['2009-06-07T20:15:14', '2010-01-01T01:05:04', false],
        ];
        foreach ($tests as $test) {
            $a = CivilDateTime::fromIsoDateTimeStamp($test[0]);
            $b = CivilDateTime::fromIsoDateTimeStamp($test[1]);
            $this->assertSame($a->laterThan($b), $test[2]);
        }
    }
}
