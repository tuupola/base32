# Base32

This library implements Base32 encoding. In addition to integers it can encode and decode any arbitrary data.

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base32.svg?style=flat-square)](https://packagist.org/packages/tuupola/base32)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base32/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base32)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/base32.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/base32)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/base32.svg?style=flat-square)](https://codecov.io/github/tuupola/base32)

## Install

Install with [composer](https://getcomposer.org/). There is no official release yet so you must use `dev-master`.

``` bash
$ composer require tuupola/base32:dev-master
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base32 = new Tuupola\Base32;

$encoded = $base32->encode(random_bytes(128));
$decoded = $base32->decode($encoded);
```

Note that if you are encoding to and from integer you need to pass boolean `true` as the second argument for `decode()` method. This is because `decode()` method does not know if the original data was an integer or binary data.

``` php
$integer = $base32->encode(987654321); /* 5N42FR== */
print $base32->decode("5N42FR==", true); /* 987654321 */
```

Also note that encoding a string and an integer will yield different results.

``` php
$integer = $base32->encode(987654321); /* 5N42FR== */
$string = $base32->encode("987654321"); /* FHE4DONRVGQZTEMI= */
```

## Character Sets

By default Base32 uses RFC4648 character set. Shortcut is provided for other commonly used character sets. You can also use any custom character set of 32 unique characters.

```php
use Tuupola\Base32;

print Base32::CROCKFORD; /* 0123456789ABCDEFGHJKMNPQRSTVWXYZ */
print Base32::RFC4648; /* ABCDEFGHIJKLMNOPQRSTUVWXYZ234567 */
print Base32::ZBASE32; /* YBNDRFG8EJKMCPQXOT1UWISZA345H769 */
print Base32::GMP; /* 0123456789ABCDEFGHIJKLMNOPQRSTUV */
print Base32::HEX; /* 0123456789ABCDEFGHIJKLMNOPQRSTUV */

$default = new Base32(["characters" => Base32::RFC4648]);
$crockford = new Base32(["characters" => Base32::CROCKFORD]);
print $default->encode("Hello world!"); /* JBSWY3DPEB3W64TMMQQQ==== */
print $inverted->encode("Hello world!"); /* 91JPRV3F41VPYWKCCGGG==== */
```

## Speed

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiosity. It is too slow to be usable.

```
$ phpbench run benchmarks/ --report=default

+-----------------------+-----------------+----------------+
| subject               | mean            | diff           |
+-----------------------+-----------------+----------------+
| benchGmpEncoder       | 73,099.415ops/s | 0.00%          |
| benchGmpEncoderCustom | 61,349.693ops/s | +19.15%        |
| benchPhpEncoder       | 25.192ops/s     | +290,072.37%   |
| benchBcmathEncoder    | 7.264ops/s      | +1,006,253.07% |
+-----------------------+-----------------+----------------+
```

## Static Proxy

If you prefer to use static syntax use the provided static proxy.

``` php
use Tuupola\Base32Proxy as Base32;

$encoded = Base32::encode(random_bytes(128));
$decoded = Base32::decode($encoded);
```

## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ composer test
```

``` bash
$ brew install entr
$ composer watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
