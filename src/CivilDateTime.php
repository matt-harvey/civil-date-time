<?php

declare(strict_types=1);

namespace MattHarvey\CivilDateTime;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use MattHarvey\CivilDateTime\Internal\Format;

class CivilDateTime
{
    public function __construct(
        private CivilDate $civilDate,
        private CivilTime $civilTime,
    )
    {
    }

    /**
     * For a given datetime and timezone, returns the civil date time at which that instant occurs for a person in
     * that timezone.
     */
    public static function forMomentInTimezone(DateTimeInterface $dateTime, DateTimeZone $timezone): self
    {
        $dt = (new DateTime())->setTimestamp($dateTime->getTimestamp())->setTimezone($timezone);
        return self::fromDateTimeInterface($dt);
    }

    /**
     * For a given timezone, returns the DateTimeImmutable at which this CivilDateTime occurs
     * in that timezone.
     */
    public function toDateTimeImmutable(DateTimeZone $timezone): DateTimeImmutable
    {
        // TODO Define what happens in the edge case during transition of daylight saving time when clock is wound back
        // and the same civil datetime can occur at two different UTC datetimes.
        return (new DateTimeImmutable())
            ->setTimezone($timezone)
            ->setDate($this->getYear(), $this->getMonth(), $this->getDay())
            ->setTime($this->get24Hour(), $this->getMinute(), $this->getSecond());
    }

    /**
     * Naively converts a date time interface to a civil date time, ignoring timezone information.
     * For example: "2010-03-15 Australia/Sydney" simply becomes "2010-03-15".
     */
    private static function fromDateTimeInterface(DateTimeInterface $dateTime): self
    {
        $civilDate = CivilDate::fromIsoDateStamp($dateTime->format(Format::ISO_DATE));
        $civilTime = CivilTime::from24HoursStamp($dateTime->format(Format::FULL_24_HOURS_TIME));
        assert($civilDate !== null);
        assert($civilTime !== null);
        return new self($civilDate, $civilTime);
    }

    public static function fromIsoDateTimeStamp(string $dateTimeStamp): ?self
    {
        $parts = explode('T', $dateTimeStamp);
        if (count($parts) != 2) {
            return null;
        }
        assert($parts[0] !== null);
        $civilDate = CivilDate::fromIsoDateStamp($parts[0]);
        if ($civilDate === null) {
            return null;
        }
        assert($parts[1] !== null);
        $civilTime = CivilTime::from24HoursStamp($parts[1]);
        if ($civilTime === null) {
            return null;
        }
        return new self($civilDate, $civilTime);
    }

    public function toIsoDateTimeStamp(): string
    {
        return $this->civilDate->toIsoDateStamp() . 'T' . $this->civilTime->to24HourStamp();
    }

    public function getYear(): int
    {
        return $this->civilDate->getYear();
    }

    public function getMonth(): int
    {
        return $this->civilDate->getMonth();
    }

    public function getDay(): int
    {
        return $this->civilDate->getDay();
    }

    public function get24Hour(): int
    {
        return $this->civilTime->get24Hour();
    }

    public function get12Hour(): int
    {
        return $this->civilTime->get12Hour();
    }

    public function getMinute(): int
    {
        return $this->civilTime->getMinute();
    }

    public function getSecond(): int
    {
        return $this->civilTime->getSecond();
    }

    public function getAmPm(): string
    {
        return $this->civilTime->getAmPm();
    }

    public function laterThan(CivilDateTime $rhs): bool
    {
       if ($this->civilDate->laterThan($rhs->civilDate)) return true;
       if ($rhs->civilDate->laterThan($this->civilDate)) return false;
       if ($this->civilTime->laterThan($rhs->civilTime)) return true;
       if ($rhs->civilTime->laterThan($this->civilTime)) return false;
       return false;
    }

    public function __toString(): string
    {
        return $this->toIsoDateTimeStamp();
    }
}
