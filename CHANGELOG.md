# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [2.0.0](https://github.com/tuupola/base32/compare/1.0.0...2.0.0) - 2020-11-19

### Added
- Allow installing with PHP 8 ([#21](https://github.com/tuupola/base32/pull/21/files)).

### Changed
- PHP 7.1 is now minimum requirement ([#11](https://github.com/tuupola/base32/pull/11)).
- All methods are typehinted and have return types ([#13](https://github.com/tuupola/base32/pull/13)).
- String and integer methods are now separated ([#14](https://github.com/tuupola/base32/pull/14)).
- Trying to decode an empty string as integer now throws an exception ([#16](https://github.com/tuupola/base32/pull/16)).


## [1.0.0](https://github.com/tuupola/base32/compare/0.3.0...1.0.0) - 2020-02-05

This is same as previous but released as stable.

## [0.3.0](https://github.com/tuupola/base32/compare/0.2.0...0.3.0) - 2019-10-24

### Added
- Support for Crockford mode ([#8](https://github.com/tuupola/base32/pull/8)).
- Possibility to force integer encoding ([#9](https://github.com/tuupola/base32/pull/8)).

## [0.2.0](https://github.com/tuupola/base32/compare/0.1.1...0.2.0) - 2019-05-05

### Added
- Implicit `decodeInteger()` and `encodeInteger()` methods ([#4](https://github.com/tuupola/base32/pull/4)).
- Character set validation for configuration ([#6](https://github.com/tuupola/base32/pull/6)).
- Character set validation for incoming data ([#7](https://github.com/tuupola/base32/pull/7)).

### Removed
- The unused and undocumented `$options` parameter from static proxy methods ([#5](https://github.com/tuupola/base32/pull/5)).

## [0.1.1](https://github.com/tuupola/base32/compare/0.1.0...0.1.1) - 2018-04-13

### Fixed
- Removed `robinvdvleuten/ulid` and `lewiscowles/ulid` from composer.json. These were accidentally included.

## 0.1.0 - 2016-06-26

Initial realese.
