# civil-date-time

[![Github actions Build](https://github.com/matt-harvey/civil-date-time/workflows/tests/badge.svg)](https://github.com/matt-harvey/civil-date-time/actions/workflows/tests.yml)
[![Latest Stable Version](http://poser.pugx.org/matt-harvey/civil-date-time/v)](https://packagist.org/packages/matt-harvey/civil-date-time)
[![Total Downloads](http://poser.pugx.org/matt-harvey/civil-date-time/downloads)](https://packagist.org/packages/matt-harvey/civil-date-time)
[![Latest Unstable Version](http://poser.pugx.org/matt-harvey/civil-date-time/v/unstable)](https://packagist.org/packages/matt-harvey/civil-date-time)
[![License](http://poser.pugx.org/matt-harvey/civil-date-time/license)](https://packagist.org/packages/matt-harvey/civil-date-time)
[![PHP Version Require](http://poser.pugx.org/matt-harvey/civil-date-time/require/php)](https://packagist.org/packages/matt-harvey/civil-date-time)

> a civil date and time library for PHP

## Motivation

A _civil date_, or civil time, is a date/time without any timezone specified.

Such an entity should not be used for representing a precise, absolute moment or period in time.
However, it is a good way to represent the concept of a "calendar date" or "clock time" as it is
used in many everyday contexts.

For example, if someone was born on 1 January 2000, then they will typically want to celebrate their
birthday on 1 January each year, in _whichever_ timezone they happen to be living in at the time. In
other words, the entity "1 January 2000", insofar as it represents their date of birth, is
timezone-agnostic.

In a software context, this might be important if, for example, we want to send an email to each
user on their birthday, where each user may have a different timezone associated with them. A given
user's timezone might change over time; but their date of birth will not. It is with the user that
the timezone should be associated here&mdash;not with their date of birth.

While it's possible to use the standard library's `DateTime` or `DateTimeImmutable` to represent
civil dates and times in PHP, it isn't ideal, as it requires an out-of-band convention about how to
interpret the timezone information attached to such an object. (Should the timezone data _merely_ be
ignored? Should the date time be converted to UTC and _then_ have its timezone ignored? Or should
non-UTC `DateTime`s be considered invalid as representations of civil dates?)

A dedicated class, that omits timezone information by design, allows civil dates, times, and date-time
pairings, to be represented cleanly and directly.

For additional concrete use cases for civil dates, times, and date-times, see
[this](https://github.com/golang/go/issues/19700#issuecomment-557820096)
comment in relation to a similar library proposed for Go.

## Installation

```
composer require matt-harvey/civil-date-time
```

Note this library is still in a pre-v1 state, and there may be breaking changes in any release.
(Although, I will generally avoid making breaking changes in patch version releases.)

## Usage

### Civil dates

```php
use MattHarvey\CivilDateTime\CivilDate;

// 15 March 2022
new CivilDate(2022, 3, 15);

// or...
CivilDate::fromIsoDateStamp('2022-03-15');

// format - signature mirrors \DateTime::format
CivilDate::fromIsoDateStamp('2022-03-15')->format('D j M Y'); // 'Sat 15 Mar 2022'

// convenience method for ISO format
CivilDate::fromIsoDateStamp('2022-03-15')->toIsoDateStamp(); // 2022-03-15
// or just
CivilDate::fromIsoDateStamp('2022-03-15')->__toString(); // 2022-03-15

// converting from standard library \DateTimeInterface to CivilDate
// e.g., the moment that is 1:30pm in UTC on 22 Jan. 2021, falls on 23 January 2021 in Sydney
$dateTime = new DateTimeImmutable('2021-01-22 13:30:01+0');
$sydney = new DateTimeZone('Australia/Sydney');
CivilDate::forMomentInTimezone($dateTime, $sydney); // 23 January 2021

// immutable addition/subtraction of days
CivilDate::fromIsoDateStamp('2022-03-15')->addDays(3); // 18 March 2022
CivilDate::fromIsoDateStamp('2022-03-15')->addDays(-3); // 12 March 2022

// difference in days
$dayA = CivilDate::fromIsoDateStamp('2010-03-05');
$dayB = CivilDate::fromIsoDateStamp('2010-04-05');
CivilDate::diffDays($dayB, $dayA); // 31
CivilDate::diffDays($dayA, $dayB); // -31

// comparison
$dayB->laterThan($dayA); // true

// extracting components
$dayA->getYear(); // 2010
$dayA->getMonth(); // 3
$dayA->getDay(); // 5
```

### Civil times

```php
use MattHarvey\CivilDateTime\CivilTime;

$civilTime = new CivilTime(22, 11, 18);                // 10:11:18 p.m.
CivilTime::from24HoursStamp('22:11:18');               // 10:11:18 p.m.
CivilTime::from12HourClock(10, 11, 18, CivilTime::PM); // 10:11:18 p.m.

$civilTime->get24Hour();                               // 22
$civilTime->get12Hour();                               // 10
$civilTime->getMinute();                               // 11
$civilTime->getSecond();                               // 18
$civilTime->getAmPm();                                 // 'pm'

$civilTime->to24HourStamp();                           // '22:11:18'

$civilTimeB = new CivilTime(22, 11, 19);
$civilTimeB->laterThan($civilTime);                    // true
```

### Civil date/times

```php
use MattHarvey\CivilDateTime\CivilTime;
use MattHarvey\CivilDateTime\CivilDate;
use MattHarvey\CivilDateTime\CivilDateTime;

$civilDate = new CivilDate(2022, 3, 15);
$civilTime = new CivilTime(22, 11, 18);
$civilDateTime = new CivilDateTime($civilDate, $civilTime); // 10:11:18 p.m. on 15 Mar. 2022
CivilDateTime::fromIsoDateTimeStamp('2022-03-15T22:11:18');

$sydney = new DateTimeZone('Australia/Sydney');
$civilDateTime = new DateTimeImmutable('2021-06-26 20:34:05+10');
CivilDateTime::forMomentInTimezone($dateTime, $sydney); // 26 Jun. 2021, 8:34:05pm

// convert back
$civilDateTime->toDateTimeImmutable('Australia/Sydney'); // 26 Jun. 2021, 8:34:05pm, Australia/Sydney
// or
$civilDateTime->toDateTimeImmutable('Australia/Perth');  // 26 Jun. 2021, 8:34:05pm, Australia/Perth
```

## Civil date/time in other PHP libraries

* https://github.com/brick/date-time offers the `LocalDate`, `LocalTime` and `LocalDateTime`
  classes, that are analogous to `civil-date-time`'s `CivilDate`, `CivilTime` and `CivilDateTime`. It also offers
  classes for modelling many other date/time concepts, which `civil-date-time` doesn't
  do&mdash;including `ZonedDateTime` as a replacement for the standard library
  `DateTimeImmutable`. You might prefer it to `civil-date-time` if you're looking for a more extensive set of date/time
  utilities, rather than a minimal adjunct to the standard library to plug just the civil date/time gap.
* https://github.com/cakephp/chronos offers a `Date` class, which is notionally similar to `civil-date-time`'s
  `CivilDate`; however it extends `DateTimeImmutable` and so carries a lot of redundant data
  as well as no-op functions inherited from the latter.

## Civil date/time libraries in other programming languages

* Go: https://pkg.go.dev/cloud.google.com/go/civil
* C++: https://github.com/google/cctz

## Contributing

PRs, bug reports, and suggestions are all welcome. Please ensure unit test coverage is maintained.

You will need `xdebug` installed to generate the coverage report.

`composer test` runs the test suite.
