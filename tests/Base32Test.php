<?php

declare(strict_types = 1);

/*

Copyright (c) 2017-2025 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/base32
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base32;

use InvalidArgumentException;
use Tuupola\Base32;
use Tuupola\Base32Proxy;
use PHPUnit\Framework\TestCase;

class Base32Test extends TestCase
{
    protected function tearDown(): void
    {
        Base32Proxy::$options = [
            "characters" => Base32::RFC4648,
            "padding" => "=",
        ];
    }

    public function testShouldBeTrue(): void
    {
        $this->assertTrue(true);
    }

    public function testPhpShouldEncodeFoobar(): void
    {
        $encoded = (new PhpEncoder)->encode("f");
        $this->assertEquals("MY======", $encoded);
        $encoded = (new PhpEncoder)->encode("fo");
        $this->assertEquals("MZXQ====", $encoded);
        $encoded = (new PhpEncoder)->encode("foo");
        $this->assertEquals("MZXW6===", $encoded);
        $encoded = (new PhpEncoder)->encode("foob");
        $this->assertEquals("MZXW6YQ=", $encoded);
        $encoded = (new PhpEncoder)->encode("fooba");
        $this->assertEquals("MZXW6YTB", $encoded);
        $encoded = (new PhpEncoder)->encode("foobar");
        $this->assertEquals("MZXW6YTBOI======", $encoded);
        $encoded = (new PhpEncoder)->encode("");
        $this->assertEquals("", $encoded);

        $encoded = (new GmpEncoder)->encode("f");
        $this->assertEquals("MY======", $encoded);
        $encoded = (new GmpEncoder)->encode("fo");
        $this->assertEquals("MZXQ====", $encoded);
        $encoded = (new GmpEncoder)->encode("foo");
        $this->assertEquals("MZXW6===", $encoded);
        $encoded = (new GmpEncoder)->encode("foob");
        $this->assertEquals("MZXW6YQ=", $encoded);
        $encoded = (new GmpEncoder)->encode("fooba");
        $this->assertEquals("MZXW6YTB", $encoded);
        $encoded = (new GmpEncoder)->encode("foobar");
        $this->assertEquals("MZXW6YTBOI======", $encoded);
        $encoded = (new GmpEncoder)->encode("");
        $this->assertEquals("", $encoded);
    }

    public function testShouldDecodeFoobar(): void
    {
        $decoded = (new PhpEncoder)->decode("MY======");
        $this->assertEquals("f", $decoded);
        $decoded = (new PhpEncoder)->decode("MZXQ====");
        $this->assertEquals("fo", $decoded);
        $decoded = (new PhpEncoder)->decode("MZXW6===");
        $this->assertEquals("foo", $decoded);
        $decoded = (new PhpEncoder)->decode("MZXW6YQ=");
        $this->assertEquals("foob", $decoded);
        $decoded = (new PhpEncoder)->decode("MZXW6YTB");
        $this->assertEquals("fooba", $decoded);
        $decoded = (new PhpEncoder)->decode("MZXW6YTBOI======");
        $this->assertEquals("foobar", $decoded);
        $decoded = (new PhpEncoder)->decode("");
        $this->assertEquals("", $decoded);

        $decoded = (string) (new GmpEncoder)->decode("MY======");
        $this->assertEquals("f", $decoded);
        $decoded = (new GmpEncoder)->decode("MZXQ====");
        $this->assertEquals("fo", $decoded);
        $decoded = (new GmpEncoder)->decode("MZXW6===");
        $this->assertEquals("foo", $decoded);
        $decoded = (new GmpEncoder)->decode("MZXW6YQ=");
        $this->assertEquals("foob", $decoded);
        $decoded = (new GmpEncoder)->decode("MZXW6YTB");
        $this->assertEquals("fooba", $decoded);
        $decoded = (new GmpEncoder)->decode("MZXW6YTBOI======");
        $this->assertEquals("foobar", $decoded);
        $decoded = (new GmpEncoder)->decode("");
        $this->assertEquals("", $decoded);
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeRandomBytes($configuration): void
    {
        $data = random_bytes(128);

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base32->decode($encoded4));
        $this->assertEquals($data, Base32Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeIntegers($configuration): void
    {
        $data = 987654321;

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base32->encodeInteger($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base32->decodeInteger($encoded4));
        $this->assertEquals($data, Base32Proxy::decodeInteger($encoded5));
    }

    public function testShouldAutoSelectEncoder(): void
    {
        $data = random_bytes(128);
        $encoded = (new Base32)->encode($data);
        $decoded = (new Base32)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    public function testShouldUseDefaultCharacterSet(): void
    {
        $data = "Hello world!";

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $base32 = new Base32();

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded2, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded4, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded5, "JBSWY3DPEB3W64TMMQQQ====");

        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded2, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded4, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded5, "AAAACAQDAQCQM===");
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeBigIntegers($configuration): void
    {
        $data = PHP_INT_MAX;

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base32->encodeInteger($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base32->decodeInteger($encoded4));
        $this->assertEquals($data, Base32Proxy::decodeInteger($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeZero($configuration): void
    {
        $data = 0;

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base32->encodeInteger($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base32->decodeInteger($encoded4));
        $this->assertEquals($data, Base32Proxy::decodeInteger($encoded5));
    }

    /**
     * @dataProvider smallIntegerProvider
     */
    public function testShouldEncodeAndDecodeSmallIntegers($data): void
    {
        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $base32 = new Base32();

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base32->encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base32->decodeInteger($encoded4));
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnEncodeNegativeInteger($encoder): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot encode negative integer");
        $encoder->encodeInteger(-1);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldDecodeWithoutPadding($encoder): void
    {
        /* "foo" encoded is "MZXW6===" with padding */
        $this->assertEquals("foo", $encoder->decode("MZXW6"));

        /* "f" encoded is "MY======" with padding */
        $this->assertEquals("f", $encoder->decode("MY"));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroByte($configuration): void
    {
        $data = "\x00";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base32->decode($encoded4));
        $this->assertEquals($data, Base32Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytes($configuration): void
    {
        $data = "\x00\x00\x00";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base32->decode($encoded4));
        $this->assertEquals($data, Base32Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroBytePrefix($configuration): void
    {
        $data = "\x00\x01\x02";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base32->decode($encoded4));
        $this->assertEquals($data, Base32Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytePrefix($configuration): void
    {
        $data = "\x00\x00\x00\x01\x02";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base32 = new Base32($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);

        Base32Proxy::$options = $configuration;
        $encoded5 = Base32Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base32->decode($encoded4));
        $this->assertEquals($data, Base32Proxy::decode($encoded5));
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeEmptyString($encoder): void
    {
        $invalid = "";
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot decode empty string as integer");
        $encoder->decodeInteger($invalid);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidData($encoder): void
    {
        $invalid = "invalid~data-%@#!@*#-foo";
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data contains invalid characters");
        $encoder->decode($invalid);
    }

    /**
     * @dataProvider classProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet($class): void
    {
        $invalid = "JBSWY3DPEB3W64TMMQQQ====";
        $options = [
            "characters" => "0123456789ABCDEFGHIXKLMNOPQRSTUV"
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data contains invalid characters");
        $decoder = new $class($options);
        $decoder->decode($invalid);
    }

    /**
     * @dataProvider classProvider
     */
    public function testShouldThrowExceptionWithTooSmallCharacterSet($class): void
    {
        /* Only 31 characters. */
        $options = [
            "characters" => "123456789ABCDEFGHIJKLMNOPQRSTUV"
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Character set must 32 unique characters");
        $decoder = new $class($options);
    }

    /**
     * @dataProvider classProvider
     */
    public function testShouldThrowExceptionWitDuplicateCharactersInSet($class): void
    {
        /* Duplicate characters. */
        $options = [
            "characters" => "00123456789ABCDEFGHIJKLMNOPQRSTUV"
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Character set must 32 unique characters");
        $decoder = new $class($options);
    }

    public function testShouldHandleCrockford(): void
    {
        $encoded1 = "91JPRV3F41VPYWKCCGGJ0Y3R";
        $encoded2 = "91jprv3f41vpywkccggj0y3r";
        $encoded3 = "9ljp-rv3f4-1vpyw-kccgg-joy3r";

        $data = "Hello world! xx";
        $configuration = [
            "characters" => Base32::CROCKFORD,
            "padding" => false,
            "crockford" => true,
        ];
        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);

        $this->assertEquals($data, $php->decode($encoded1));
        $this->assertEquals($data, $php->decode($encoded2));
        $this->assertEquals($data, $php->decode($encoded3));

        $this->assertEquals($data, $gmp->decode($encoded1));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $gmp->decode($encoded3));
    }

    public static function configurationProvider(): array
    {
        return [
            "RCF4684 mode" => [[
                "characters" => Base32::RFC4648,
                "padding" => "=",
            ]],
            "RCF4684 HEX mode" => [[
                "characters" => Base32::HEX,
                "padding" => "=",
            ]],
            "GMP mode" => [[
                "characters" => Base32::GMP,
                "padding" => false,
            ]],
            "Crockford mode" => [[
                "characters" => Base32::CROCKFORD,
                "padding" => false,
                "crocford" => true,
            ]],
            "Custom character set" => [[
                "characters" => "ABCDEFGHIJKLMNOPQRSTUV0123456789",
                "padding" => false,
            ]],
        ];
    }

    public static function encoderProvider(): array
    {
        return [
            PhpEncoder::class => [new PhpEncoder()],
            GmpEncoder::class => [new GmpEncoder()],
            Base32::class => [new Base32()],
        ];
    }

    public static function classProvider(): array
    {
        return [
            PhpEncoder::class => [PhpEncoder::class],
            GmpEncoder::class => [GmpEncoder::class],
            Base32::class => [Base32::class],
        ];
    }

    public static function smallIntegerProvider(): array
    {
        return [
            "one" => [1],
            "two" => [2],
            "thirty-one (5 bits max)" => [31],
            "thirty-two (6 bits)" => [32],
            "255 (8 bits max)" => [255],
            "256 (9 bits)" => [256],
            "1023 (10 bits max)" => [1023],
            "1024 (11 bits)" => [1024],
        ];
    }
}
