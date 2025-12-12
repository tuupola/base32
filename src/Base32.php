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

namespace Tuupola;

use Tuupola\Base32\BaseEncoder;

class Base32
{
    public const CROCKFORD = "0123456789ABCDEFGHJKMNPQRSTVWXYZ";
    public const RFC4648 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
    public const ZBASE32 = "ybndrfg8ejkmcpqxot1uwisza345h769";
    public const GMP = "0123456789ABCDEFGHIJKLMNOPQRSTUV";
    public const HEX = "0123456789ABCDEFGHIJKLMNOPQRSTUV";

    private BaseEncoder $encoder;

    /**
      * @param string $characters Character set to use for encoding
      * @param string|false $padding Padding character or false if not used
      * @param bool $crockford Use Crockford encoding if true
      */
    public function __construct(
        private string $characters = Base32::RFC4648,
        private string|false $padding = "=",
        private bool $crockford = false
    ) {
        if (function_exists("gmp_init")) {
            $this->encoder = new Base32\GmpEncoder(
                characters: $this->characters,
                padding: $this->padding,
                crockford: $this->crockford
            );
        }
        $this->encoder = new Base32\PhpEncoder(
            characters: $this->characters,
            padding: $this->padding,
            crockford: $this->crockford
        );
    }

    /**
     * Encode given data to a base32 string
     */
    public function encode(string $data): string
    {
        return $this->encoder->encode($data);
    }

    /**
     * Decode given a base32 string back to data
     */
    public function decode(string $data): string
    {
        return $this->encoder->decode($data);
    }

    /**
     * Encode given integer to a base32 string
     */
    public function encodeInteger(int $data): string
    {
        return $this->encoder->encodeInteger($data);
    }

    /**
     * Decode given base32 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        return $this->encoder->decodeInteger($data);
    }
}
