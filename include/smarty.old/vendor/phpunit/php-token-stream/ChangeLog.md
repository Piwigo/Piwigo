# Change Log

All notable changes to `sebastianbergmann/php-token-stream` are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [4.0.4] - 2020-08-04

### Added

* Support for `NAME_FULLY_QUALIFIED`, `NAME_QUALIFIED`, and `NAME_RELATIVE` tokens

## [4.0.3] - 2020-06-27

### Added

* This component is now supported on PHP 8

## [4.0.2] - 2020-06-16

### Fixed

* Fixed backward compatibility breaks introduced in version 4.0.1

## [4.0.1] - 2020-05-06

### Fixed

* [#93](https://github.com/sebastianbergmann/php-token-stream/issues/93): Class with method that uses anonymous class is not processed correctly

## [4.0.0] - 2020-02-07

### Removed

* This component is no longer supported PHP 7.1 and PHP 7.2

## [3.1.1] - 2019-09-17

### Fixed

* [#84](https://github.com/sebastianbergmann/php-token-stream/issues/84): Methods named `class` are not handled correctly

## [3.1.0] - 2019-07-25

### Added

* Added support for `FN` and `COALESCE_EQUAL` tokens introduced in PHP 7.4

## [3.0.2] - 2019-07-08

### Changed

* [#82](https://github.com/sebastianbergmann/php-token-stream/issues/82): Make sure this component works when its classes are prefixed using php-scoper

## [3.0.1] - 2018-10-30

### Fixed

* [#78](https://github.com/sebastianbergmann/php-token-stream/pull/78): `getEndTokenId()` does not handle string-dollar (`"${var}"`) interpolation

## [3.0.0] - 2018-02-01

### Removed

* [#71](https://github.com/sebastianbergmann/php-token-stream/issues/71): Remove code specific to Hack language constructs
* [#72](https://github.com/sebastianbergmann/php-token-stream/issues/72): Drop support for PHP 7.0

## [2.0.2] - 2017-11-27

### Fixed

* [#69](https://github.com/sebastianbergmann/php-token-stream/issues/69): `PHP_Token_USE_FUNCTION` does not serialize correctly

## [2.0.1] - 2017-08-20

### Fixed

* [#68](https://github.com/sebastianbergmann/php-token-stream/issues/68): Method with name `empty` wrongly recognized as anonymous function

## [2.0.0] - 2017-08-03

[4.0.4]: https://github.com/sebastianbergmann/php-token-stream/compare/4.0.3...4.0.4
[4.0.3]: https://github.com/sebastianbergmann/php-token-stream/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/sebastianbergmann/php-token-stream/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/sebastianbergmann/php-token-stream/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/sebastianbergmann/php-token-stream/compare/3.1.1...4.0.0
[3.1.1]: https://github.com/sebastianbergmann/php-token-stream/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/sebastianbergmann/php-token-stream/compare/3.0.2...3.1.0
[3.0.2]: https://github.com/sebastianbergmann/php-token-stream/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/php-token-stream/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/php-token-stream/compare/2.0...3.0.0
[2.0.2]: https://github.com/sebastianbergmann/php-token-stream/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/sebastianbergmann/php-token-stream/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/sebastianbergmann/php-token-stream/compare/1.4.11...2.0.0
