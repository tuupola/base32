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

class Base32Test extends \PHPUnit_Framework_TestCase
{

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
    }

    public function testShouldDecodeFoobar()
    {
        $decoded = (string) (new PhpEncoder)->decode("MY======");
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
    }


    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        //$encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        //$decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        //$this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        //$this->assertEquals($data, $decoded3);

        $encoded4 = (new Base32)->encode($data);
        $decoded4 = (new Base32)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        // $encoded5 = Base32Proxy::encode($data);
        // $decoded5 = Base32Proxy::decode($encoded5);
        // $this->assertEquals($encoded, $encoded5);
        // $this->assertEquals($data, $decoded5);
    }

    // public function testShouldEncodeAndDecodeIntegers()
    // {
    //     $data = 987654321;
    //     $encoded = (new PhpEncoder)->encode($data);
    //     $encoded2 = (new GmpEncoder)->encode($data);
    //     $encoded3 = (new BcmathEncoder)->encode($data);
    //     $decoded = (new PhpEncoder)->decode($encoded, true);
    //     $decoded2 = (new GmpEncoder)->decode($encoded2, true);
    //     $decoded3 = (new BcmathEncoder)->decode($encoded2, true);

    //     $this->assertEquals($decoded2, $decoded);
    //     $this->assertEquals($decoded3, $decoded);
    //     $this->assertEquals($data, $decoded);
    //     $this->assertEquals($data, $decoded2);
    //     $this->assertEquals($data, $decoded3);

    //     $encoded4 = (new Base32)->encode($data);
    //     $decoded4 = (new Base32)->decode($encoded4, true);
    //     $this->assertEquals($data, $decoded4);

    //     $encoded5 = Base32Proxy::encode($data);
    //     $decoded5 = Base32Proxy::decode($encoded5, true);
    //     $this->assertEquals($encoded, $encoded5);
    //     $this->assertEquals($data, $decoded5);
    // }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base32)->encode($data);
        $decoded = (new Base32)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        //$encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        //$decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        //$this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        //$this->assertEquals($data, $decoded3);

        $encoded4 = (new Base32)->encode($data);
        $decoded4 = (new Base32)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        // $encoded5 = Base32Proxy::encode($data);
        // $decoded5 = Base32Proxy::decode($encoded5);
        // $this->assertEquals($encoded, $encoded5);
        // $this->assertEquals($data, $decoded5);
    }

    // public function testShouldUseDefaultCharacterSet()
    // {
    //     $data = "Hello world!";

    //     $encoded = (new PhpEncoder)->encode($data);
    //     $encoded2 = (new GmpEncoder)->encode($data);
    //     $encoded3 = (new BcmathEncoder)->encode($data);
    //     $decoded = (new PhpEncoder)->decode($encoded);
    //     $decoded2 = (new GmpEncoder)->decode($encoded2);
    //     $decoded3 = (new BcmathEncoder)->decode($encoded2);

    //     $this->assertEquals($encoded, "T8dgcjRGuYUueWht");
    //     $this->assertEquals($encoded2, "T8dgcjRGuYUueWht");
    //     $this->assertEquals($encoded3, "T8dgcjRGuYUueWht");
    //     $this->assertEquals($data, $decoded);
    //     $this->assertEquals($data, $decoded2);
    //     $this->assertEquals($data, $decoded3);

    //     $encoded4 = (new Base32)->encode($data);
    //     $decoded4 = (new Base32)->decode($encoded4);
    //     $this->assertEquals($data, $decoded4);

    //     $encoded5 = Base32Proxy::encode($data);
    //     $decoded5 = Base32Proxy::decode($encoded5);
    //     $this->assertEquals($encoded, $encoded5);
    //     $this->assertEquals($data, $decoded5);
    // }

    // public function testShouldUseInvertedCharacterSet()
    // {
    //     $data = "Hello world!";

    //     $encoded = (new PhpEncoder(["characters" => Base32::INVERTED]))->encode($data);
    //     $encoded2 = (new GmpEncoder(["characters" => Base32::INVERTED]))->encode($data);
    //     $encoded3 = (new BcmathEncoder(["characters" => Base32::INVERTED]))->encode($data);
    //     $decoded = (new PhpEncoder(["characters" => Base32::INVERTED]))->decode($encoded);
    //     $decoded2 = (new GmpEncoder(["characters" => Base32::INVERTED]))->decode($encoded2);
    //     $decoded3 = (new BcmathEncoder(["characters" => Base32::INVERTED]))->decode($encoded2);

    //     $this->assertEquals($encoded, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded2, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded3, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($data, $decoded);
    //     $this->assertEquals($data, $decoded2);
    //     $this->assertEquals($data, $decoded3);

    //     $encoded4 = (new Base32)->encode($data);
    //     $decoded4 = (new Base32)->decode($encoded4);
    //     $this->assertEquals($data, $decoded4);

    //     Base32Proxy::$options = [
    //         "characters" => Base32::INVERTED,
    //     ];
    //     $encoded5 = Base32Proxy::encode($data);
    //     $decoded5 = Base32Proxy::decode($encoded5);
    //     $this->assertEquals($encoded5, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($data, $decoded5);
    // }

    // public function testShouldUseICustomCharacterSet()
    // {
    //     $data = "Hello world!";
    //     $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    //     $encoded = (new PhpEncoder(["characters" => $characters]))->encode($data);
    //     $encoded2 = (new GmpEncoder(["characters" => $characters]))->encode($data);
    //     $encoded3 = (new BcmathEncoder(["characters" => $characters]))->encode($data);
    //     $decoded = (new PhpEncoder(["characters" => $characters]))->decode($encoded);
    //     $decoded2 = (new GmpEncoder(["characters" => $characters]))->decode($encoded2);
    //     $decoded3 = (new BcmathEncoder(["characters" => $characters]))->decode($encoded2);

    //     $this->assertEquals($encoded, "DiNQMTBq4IE4OGR3");
    //     $this->assertEquals($encoded2, "DiNQMTBq4IE4OGR3");
    //     $this->assertEquals($encoded3, "DiNQMTBq4IE4OGR3");
    //     $this->assertEquals($data, $decoded);
    //     $this->assertEquals($data, $decoded2);
    //     $this->assertEquals($data, $decoded3);

    //     $encoded4 = (new Base32)->encode($data);
    //     $decoded4 = (new Base32)->decode($encoded4);
    //     $this->assertEquals($data, $decoded4);

    //     Base32Proxy::$options = [
    //         "characters" => $characters,
    //     ];
    //     $encoded5 = Base32Proxy::encode($data);
    //     $decoded5 = Base32Proxy::decode($encoded5);
    //     $this->assertEquals($encoded5, "DiNQMTBq4IE4OGR3");
    //     $this->assertEquals($data, $decoded5);
    // }
}
