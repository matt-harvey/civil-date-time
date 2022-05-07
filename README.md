# civil-date-time

> Civil date and time library for PHP

A civil date (or time) is a date or time without any timezone specified.

Such an entity should not be used for representing a precise, absolute moment or period in time.
However, it is a good way to represent the concept of a _calendar_ date or "clock time" as it is
used in many everyday contexts. For example, if someone says that Christmas is on the 25th of
December, they mean only that it's that date in the calendar; and that one might celebrate it on that
calendar date regardless of which timezone one happens finds oneself in.

While it's possible to use the standard library's `DateTime` or `DateTimeImmutable` to represent
civil dates/times in PHP, it isn't ideal; since it requires an out-of-band convention about how to
interpret the timezone information attached to such an object in such as not to cause cause error or
confusion. A dedicated class allows civil dates, times, and date-time pairings, to be represented
directly without any particular timezone attached.
