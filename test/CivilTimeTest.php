<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MattHarvey\CivilDateTime\CivilTime;

final class CivilTimeTest extends TestCase
{
    public function testFrom24HoursStamp(): void
    {
        $stamp = '13:56:00';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(13, $time?->get24Hour());
        $this->assertSame(1, $time?->get12Hour());
        $this->assertSame(56, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());

        $stamp = '01:05:23';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(1, $time?->get24Hour());
        $this->assertSame(1, $time?->get12Hour());
        $this->assertSame(5, $time?->getMinute());
        $this->assertSame(23, $time?->getSecond());

        $stamp = '00:01:55';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(0, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(1, $time?->getMinute());
        $this->assertSame(55, $time?->getSecond());

        $stamp = '12:01';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(null, $time);

        $stamp = '12:020';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(null, $time);

        $stamp = '10:50:06';
        $time = CivilTime::from24HoursStamp($stamp);
        $this->assertSame(10, $time?->get24Hour());
        $this->assertSame(10, $time?->get12Hour());
        $this->assertSame(50, $time?->getMinute());
        $this->assertSame(6, $time?->getSecond());
    }

    public function testFrom24HoursWithoutSecondsStamp(): void
    {
        $stamp = '13:56';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(13, $time?->get24Hour());
        $this->assertSame(1, $time?->get12Hour());
        $this->assertSame(56, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());

        $stamp = '01:00';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(1, $time?->get24Hour());
        $this->assertSame(1, $time?->get12Hour());
        $this->assertSame(0, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());

        $stamp = '00:01';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(0, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(1, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());

        $stamp = '0:01';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(null, $time);

        $stamp = '12:02';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(12, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(2, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());

        $stamp = '10:50:06';
        $time = CivilTime::from24HoursWithoutSecondsStamp($stamp);
        $this->assertSame(null, $time);
    }

    public function testFrom12HourClock(): void
    {
        $time = CivilTime::from12HourClock(12, 15, 0, 'pm');
        $this->assertSame(12, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(15, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('pm', $time?->getAmPm());

        $time = CivilTime::from12HourClock(12, 15, 0, 'am');
        $this->assertSame(0, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(15, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('am', $time?->getAmPm());

        $time = CivilTime::from12HourClock(12, 0, 0, 'am');
        $this->assertSame(0, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(0, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('am', $time?->getAmPm());

        $time = CivilTime::from12HourClock(12, 0, 5, 'am');
        $this->assertSame(0, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(0, $time?->getMinute());
        $this->assertSame(5, $time?->getSecond());
        $this->assertSame('am', $time?->getAmPm());

        $time = CivilTime::from12HourClock(12, 0, 0, 'pm');
        $this->assertSame(12, $time?->get24Hour());
        $this->assertSame(12, $time?->get12Hour());
        $this->assertSame(0, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('pm', $time?->getAmPm());

        $time = CivilTime::from12HourClock(10, 51, 0, 'pm');
        $this->assertSame(22, $time?->get24Hour());
        $this->assertSame(10, $time?->get12Hour());
        $this->assertSame(51, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('pm', $time?->getAmPm());

        $time = CivilTime::from12HourClock(10, 51, 0, 'am');
        $this->assertSame(10, $time?->get24Hour());
        $this->assertSame(10, $time?->get12Hour());
        $this->assertSame(51, $time?->getMinute());
        $this->assertSame(0, $time?->getSecond());
        $this->assertSame('am', $time?->getAmPm());

        $time = CivilTime::from12HourClock(10, 51, 0, 'jm');
        $this->assertSame(null, $time);

        $time = CivilTime::from12HourClock(18, 51, 0, 'am');
        $this->assertSame(null, $time);

        $time = CivilTime::from12HourClock(1, -6, 0, 'am');
        $this->assertSame(null, $time);
    }

    public function testTo24HourStamp(): void
    {
        $time = CivilTime::from12HourClock(1, 6, 0, 'am');
        $this->assertSame('01:06:00', $time?->to24HourStamp());

        $time = CivilTime::from12HourClock(12, 6, 17, 'pm');
        $this->assertSame('12:06:17', $time?->to24HourStamp());

        $time = CivilTime::from12HourClock(11, 6, 17, 'pm');
        $this->assertSame('23:06:17', $time?->to24HourStamp());

        $time = CivilTime::from12HourClock(12, 0, 5, 'am');
        $this->assertSame('00:00:05', $time?->to24HourStamp());
    }

    /** @suppress PhanTypeMismatchArgumentNullable */
    public function testLaterThan(): void
    {
        $this->assertFalse(
            CivilTime::from24HoursStamp('23:17:06')->laterThan(CivilTime::from24HoursStamp('23:17:07'))
        );
        $this->assertFalse(
            CivilTime::from24HoursStamp('23:17:06')->laterThan(CivilTime::from24HoursStamp('23:17:06'))
        );
        $this->assertTrue(
            CivilTime::from24HoursStamp('23:17:07')->laterThan(CivilTime::from24HoursStamp('23:17:06'))
        );
        $this->assertTrue(
            CivilTime::from24HoursStamp('23:18:01')->laterThan(CivilTime::from24HoursStamp('23:17:06'))
        );
        $this->assertTrue(
            CivilTime::from24HoursStamp('23:18:01')->laterThan(CivilTime::from24HoursStamp('22:57:46'))
        );
        $this->assertFalse(
            CivilTime::from24HoursStamp('08:18:01')->laterThan(CivilTime::from24HoursStamp('22:57:46'))
        );
    }
}
