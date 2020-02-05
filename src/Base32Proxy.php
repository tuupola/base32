<?php

/*

Copyright (c) 2017-2020 Mika Tuupola

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

namespace Tuupola;

use Tuupola\Base32;

class Base32Proxy
{
    public static $options = [
        "characters" => Base32::RFC4648,
    ];

    /**
     * Encode given data to a base32 string
     */
    public static function encode(string $data, bool $integer = false): string
    {
        return (new Base32(self::$options))->encode($data, $integer);
    }

    /**
     * Decode given a base32 string back to data
     */
    public static function decode(string $data, bool $integer = false): string
    {
        return (new Base32(self::$options))->decode($data, $integer);
    }

    /**
     * Encode given integer to a base32 string
     */
    public static function encodeInteger(int $data): string
    {
        return (new Base32(self::$options))->encodeInteger($data);
    }

    /**
     * Decode given base32 string back to an integer
     */
    public static function decodeInteger(string $data): int
    {
        return (new Base32(self::$options))->decodeInteger($data);
    }
}
