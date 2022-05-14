# Changelog

### v0.2.1

* Document that instances are immutable
* Use `readonly` to internally document/enforce property immutability within each class
* Various improvements in README

### v0.2.0

#### Breaking change

* When calling `CivilDateTime::toDateTimeImmutable`, instead of converting the final
  `DateTimeImmutable` instance to UTC, retain the timezone that was actually passed.

#### Other changes

* Document `CivilDateTime::toDateTimeImmutable` in the README.
* Run tests automatically on push, reporting pass/fail via README badge

### v0.1.0

Initial release
