<?php

/*
 * This file is part of the Base32 package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base32
 *
 */

namespace Tuupola\Base32;

use Tuupola\Base32;
use Tuupola\Base32Proxy;
use PHPUnit\Framework\TestCase;

class Base32Test extends TestCase
{

    protected function tearDown()
    {
        Base32Proxy::$options = [
            "characters" => Base32::RFC4648,
            "padding" => "=",
        ];
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testPhpShouldEncodeFoobar()
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
        $encoded = (new PhpEncoder)->encode(null);
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
        $encoded = (new GmpEncoder)->encode(null);
        $this->assertEquals("", $encoded);
    }

    public function testShouldDecodeFoobar()
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
        $decoded = (new PhpEncoder)->decode(null);
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
        $decoded = (new GmpEncoder)->decode(null);
        $this->assertEquals("", $decoded);
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeRandomBytes($configuration)
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
    public function testShouldEncodeAndDecodeIntegers($configuration)
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

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base32)->encode($data);
        $decoded = (new Base32)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $base32 = new Base32();
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);
        // Base32Proxy::$options = [
        //     "characters" => $configuration,
        // ];
        $encoded5 = Base32Proxy::encode($data);
        $this->assertEquals($encoded, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded2, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded4, "JBSWY3DPEB3W64TMMQQQ====");
        $this->assertEquals($encoded5, "JBSWY3DPEB3W64TMMQQQ====");
        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);
        // Base32Proxy::$options = [
        //     "characters" => $configuration,
        // ];
        $encoded5 = Base32Proxy::encode($data);
        $this->assertEquals($encoded, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded2, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded4, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded5, "AAAACAQDAQCQM===");

        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base32->encode($data);
        // Base32Proxy::$options = [
        //     "characters" => $characters,
        // ];
        $encoded5 = Base32Proxy::encode($data);
        $this->assertEquals($encoded, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded2, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded4, "AAAACAQDAQCQM===");
        $this->assertEquals($encoded5, "AAAACAQDAQCQM===");
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeBigIntegers($configuration)
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
    public function testShouldEncodeAndDecodeSingleZeroByte($configuration)
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
    public function testShouldEncodeAndDecodeMultipleZeroBytes($configuration)
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
    public function testShouldEncodeAndDecodeSingleZeroBytePrefix($configuration)
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
    public function testShouldEncodeAndDecodeMultipleZeroBytePrefix($configuration)
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

    // public function testShouldThrowExceptionOnDecodeInvalidData()
    // {
    //     $invalid = "invalid~data-%@#!@*#-foo";
    //     $decoders = [
    //         new PhpEncoder(),
    //         new GmpEncoder(),
    //         new Base85(),
    //     ];
    //     foreach ($decoders as $decoder) {
    //         $caught = null;
    //         try {
    //             $decoder->decode($invalid, false);
    //         } catch (InvalidArgumentException $exception) {
    //             $caught = $exception;
    //         }
    //         $this->assertInstanceOf(InvalidArgumentException::class, $caught);
    //     }
    // }

    // public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet()
    // {
    //     /* This would normally be valid, however the custom character set */
    //     /* is missing the e character. */
    //     $invalid = "T8dgcjRGuYUueWht";
    //     $options = [
    //         "characters" => "!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdxfghijklmnopqrstu"
    //     ];
    //     $decoders = [
    //         new PhpEncoder($options),
    //         new GmpEncoder($options),
    //         new Base85($options),
    //     ];
    //     foreach ($decoders as $decoder) {
    //         $caught = null;
    //         try {
    //             $decoder->decode($invalid, false);
    //         } catch (InvalidArgumentException $exception) {
    //             $caught = $exception;
    //         }
    //         $this->assertInstanceOf(InvalidArgumentException::class, $caught);
    //     }
    // }

    // public function testShouldThrowExceptionWithInvalidCharacterSet()
    // {
    //     $options = [
    //         "characters" => "!!#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstu"
    //     ];
    //     $decoders = [
    //         PhpEncoder::class,
    //         GmpEncoder::class,
    //         Base85::class,
    //     ];
    //     foreach ($decoders as $decoder) {
    //         $caught = null;
    //         try {
    //             new $decoder($options);
    //         } catch (InvalidArgumentException $exception) {
    //             $caught = $exception;
    //         }
    //         $this->assertInstanceOf(InvalidArgumentException::class, $caught);
    //     }
    //     $options = [
    //         "characters" => "00123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
    //     ];
    //     foreach ($decoders as $decoder) {
    //         $caught = null;
    //         try {
    //             new $decoder($options);
    //         } catch (InvalidArgumentException $exception) {
    //             $caught = $exception;
    //         }
    //         $this->assertInstanceOf(InvalidArgumentException::class, $caught);
    //     }
    // }

    public function configurationProvider()
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
            "Custom character set" => [[
                "characters" => "ABCDEFGHIJKLMNOPQRSTUV0123456789",
                "padding" => false,
            ]],
        ];
    }
}
