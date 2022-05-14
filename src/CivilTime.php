<?php

namespace MattHarvey\CivilDateTime;

use MattHarvey\CivilDateTime\Internal\StringUtil;

/**
 * An immutable representation of a "clock face time", that is not accompanied by
 * a date or timezone.
 */
class CivilTime
{
    public const AM = 'am';
    public const PM = 'pm';

    public function __construct(
        private readonly int $hour,
        private readonly int $minute,
        private readonly int $second,
    )
    {
    }

    public static function from24HoursStamp(string $s): ?self
    {
        $parts = explode(':', $s);
        if (count($parts) != 3) {
            return null;
        }
        $hoursStr = $parts[0];
        $minutesStr = $parts[1];
        $secondsStr = $parts[2];
        if (strlen($hoursStr) != 2 || strlen($minutesStr) != 2 || strlen($secondsStr) != 2) {
            return null;
        }
        $hours = filter_var(StringUtil::stripPrefix($hoursStr, '0'), FILTER_VALIDATE_INT);
        if ($hours === false) {
            return null;
        }
        $minutes = filter_var(StringUtil::stripPrefix($minutesStr, '0'), FILTER_VALIDATE_INT);
        if ($minutes === false) {
            return null;
        }
        $seconds = filter_var(StringUtil::stripPrefix($secondsStr, '0'), FILTER_VALIDATE_INT);
        if ($seconds === false) {
            return null;
        }
        return new self($hours, $minutes, $seconds);
    }

    public static function from24HoursWithoutSecondsStamp(string $s): ?self
    {
        $parts = explode(':', $s);
        if (count($parts) != 2) {
            return null;
        }
        $hoursStr = $parts[0];
        $minutesStr = $parts[1];
        if (strlen($hoursStr) != 2 || strlen($minutesStr) != 2) {
            return null;
        }
        $hours = filter_var(StringUtil::stripPrefix($hoursStr, '0'), FILTER_VALIDATE_INT);
        if ($hours === false) {
            return null;
        }
        $minutes = filter_var(StringUtil::stripPrefix($minutesStr, '0'), FILTER_VALIDATE_INT);
        if ($minutes === false) {
            return null;
        }
        return new self($hours, $minutes, 0);
    }

    public static function from12HourClock(
        int $hour,
        int $minute,
        int $second,
        string $amPm,
    ): ?self
    {
        if ($hour < 0 || $hour > 12 || $minute < 0 || $minute > 59 || $second < 0 || $second > 59) {
            return null;
        }
        if ($amPm !== self::AM && $amPm !== self::PM) {
            return null;
        }
        switch ($amPm) {
        case self::AM:
            if ($hour == 12) {
                return new self(0, $minute, $second);
            }
            return new self($hour, $minute, $second);
        case self::PM: default:
            if ($hour == 12) {
                return new self($hour, $minute, $second);
            }
            return new self($hour + 12, $minute, $second);
        }
    }

    public function get24Hour(): int
    {
        return $this->hour;
    }

    public function get12Hour(): int
    {
        switch ($this->hour) {
        case 0: case 12:
            return 12;
        default:
            return ($this->hour > 12 ? ($this->hour - 12) : $this->hour);
        }
    }

    public function getMinute(): int
    {
        return $this->minute;
    }

    public function getSecond(): int
    {
        return $this->second;
    }

    public function getAmPm(): string
    {
        if ($this->hour < 12) {
            return self::AM;
        }
        return self::PM;
    }

    public function to24HourStamp(): string
    {
        $hour = sprintf('%02d', $this->hour);
        $minute = sprintf('%02d', $this->minute);
        $second = sprintf('%02d', $this->second);
        return "$hour:$minute:$second";
    }

    public function laterThan(self $rhs): bool
    {
        switch ($this->hour <=> $rhs->hour) {
        case -1:
            return false;
        case 1:
            return true;
        case 0: default:
            switch ($this->minute <=> $rhs->minute) {
                case -1:
                    return false;
                case 1:
                    return true;
                case 0: default:
                    switch ($this->second <=> $rhs->second) {
                        case 1:
                            return true;
                        case 0: case -1: default:
                            return false;
                    }
            }
        }
    }
}
