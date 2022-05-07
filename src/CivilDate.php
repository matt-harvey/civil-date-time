<?php

declare(strict_types=1);

namespace MattHarvey\CivilDateTime;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use MattHarvey\CivilDateTime\Internal\StringUtil;

class CivilDate
{
    public function __construct(
        private int $year,
        private int $month,
        private int $day,
    )
    {
    }

    /**
     * @return ?CivilDate
     * Returns null if $dateStamp is not a valid date ISO date stamp in the format YYYY-MM-DD,
     * otherwise returns a new instance of CivilDate.
     */
    public static function fromIsoDateStamp(string $dateStamp): ?self
    {
        $parts = explode('-', $dateStamp);
        if (count($parts) != 3) {
            return null;
        }
        $year = filter_var($parts[0], FILTER_VALIDATE_INT);
        if ($year === false) {
            return null;
        }
        $month = filter_var(StringUtil::stripPrefix($parts[1], '0'), FILTER_VALIDATE_INT);
        if ($month === false) {
            return null;
        }
        $day = filter_var(StringUtil::stripPrefix($parts[2], '0'), FILTER_VALIDATE_INT);
        if ($day === false) {
            return null;
        }
        if (!checkdate($month, $day, $year)) {
            return null;
        }
        return new self(year: $year, month: $month, day: $day);
    }

    public function format(string $format): string
    {
        return (new DateTimeImmutable())->setDate($this->year, $this->month, $this->day)->format($format);
    }

    public function toIsoDateStamp(): string
    {
        $month = sprintf('%02d', $this->month);
        $day = sprintf('%02d', $this->day);
        return "{$this->year}-{$month}-{$day}";
    }

    /**
     * For a given datetime and timezone, returns the civil date during which that instant occurs for a person in
     * that timezone.
     */
    public static function forMomentInTimezone(DateTimeInterface $dateTime, DateTimeZone $timezone): self
    {
        $dt = (new DateTime())->setTimestamp($dateTime->getTimestamp())->setTimezone($timezone);
        return self::fromDateTimeInterface($dt);
    }

    /**
     * @param int $days Pass a negative number to subtract days.
     */
    public function addDays(int $days): self
    {
        if ($days == 0) {
            return $this;
        }
        $negative = ($days < 0);
        $abs = ($negative ? -$days : $days);
        $dayInterval = new DateInterval("P{$abs}D");
        $dateTime = new DateTime($this->toIsoDateStamp());
        if ($negative) {
            $dateTime->sub($dayInterval);
        } else {
            $dateTime->add($dayInterval);
        }
        return self::fromDateTimeInterface($dateTime);
    }

    /**
     * @return int the number of days between $day1 and $day2. If $day1 is later than $day2, then a
     * positive integer will be returned. If $day2 is later than $day1, then a negative integer
     * will be returned. If they are they same day, 0 will be returned.
     */
    public static function diffDays(self $day1, self $day2): int
    {
        $dateTime1 = new DateTimeImmutable($day1->toIsoDateStamp());
        $dateTime2 = new DateTimeImmutable($day2->toIsoDateStamp());
        $interval = $dateTime2->diff($dateTime1);
        return (int)$interval->format('%R%a');
    }

    public function laterThan(self $rhs): bool
    {
        switch ($this->year <=> $rhs->year) {
        case -1:
            return false;
        case 1:
            return true;
        case 0: default:
            switch ($this->month <=> $rhs->month) {
                case -1:
                    return false;
                case 1:
                    return true;
                case 0: default:
                    switch ($this->day <=> $rhs->day) {
                        case 1:
                            return true;
                        case 0: case -1: default:
                            return false;
                    }
            }
        }
    }

    public function equals(self $rhs): bool
    {
        return
            $this->year == $rhs->year &&
            $this->month == $rhs->month &&
            $this->day == $rhs->day;
    }

    private static function fromDateTimeInterface(DateTimeInterface $dateTime): self
    {
        return new self(
            year: (int)$dateTime->format('Y'),
            month: (int)$dateTime->format('n'),
            day: (int)$dateTime->format('j'),
        );
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public static function earliest(self ...$civilDates): self
    {
        $count = count($civilDates);
        if ($count == 0) {
            throw new Exception('No dates passed');
        }
        $earliest = $civilDates[0];
        for ($i = 1; $i != $count; $i++) {
            if ($earliest->laterThan($civilDates[$i])) $earliest = $civilDates[$i];
        }
        return $earliest;
    }

    public static function latest(self ...$civilDates): self
    {
        $count = count($civilDates);
        if ($count == 0) {
            throw new Exception('No dates passed');
        }
        $latest = $civilDates[0];
        for ($i = 1; $i != $count; $i++) {
            if ($civilDates[$i]->laterThan($latest)) $latest = $civilDates[$i];
        }
        return $latest;
    }

    public function __toString(): string
    {
        return $this->toIsoDateStamp();
    }
}
