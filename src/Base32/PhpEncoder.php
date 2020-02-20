<?php

declare(strict_types = 1);

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

namespace Tuupola\Base32;

use InvalidArgumentException;
use Tuupola\Base32;

class PhpEncoder extends BaseEncoder
{
    /**
     * Encode given data to a base32 string
     */
    public function encode(string $data): string
    {
        if (empty($data)) {
            return "";
        }

        /* Create binary string zeropadded to eight bits. */
        $data = str_split($data);
        $binary = implode("", array_map(function ($character) {
            return sprintf("%08b", ord($character));
        }, $data));

        /* Split to five bit chunks and make sure last chunk has five bits. */
        $binary = str_split($binary, 5);
        $last = array_pop($binary);
        if (null !== $last) {
            $binary[] = str_pad($last, 5, "0", STR_PAD_RIGHT);
        }

        /* Convert each five bits to Base32 character. */
        $encoded = implode("", array_map(function ($fivebits) {
            $index = bindec($fivebits);
            return $this->characters()[$index];
        }, $binary));

        /* Pad to eight characters when requested. */
        if (!empty($this->padding())) {
            if ($modulus = strlen($encoded) % 8) {
                $padding = 8 - $modulus;
                $encoded .= str_repeat($this->padding(), $padding);
            }
        }

        return $encoded;
    }

    /**
     * Decode given a base32 string back to data
     */
    public function decode(string $data): string
    {
        if (empty($data)) {
            return "";
        }

        if ($this->isCrockford()) {
            $data = strtoupper($data);
            $data = str_replace(["O", "L", "I", "-"], ["0", "1", "1", ""], $data);
        }

        /* If the data contains characters that aren't in the character set. */
        $characters = $this->characters() . (string) $this->padding();
        if (strlen($data) !== strspn($data, $characters)) {
            $valid = str_split($this->characters());
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);
            throw new InvalidArgumentException(
                "Data contains invalid characters \"{$invalid}\""
            );
        }

        $data = str_split($data);
        $data = array_map(function ($character) {
            if ($character !== $this->padding()) {
                $index = strpos($this->characters(), $character);
                return sprintf("%05b", $index);
            }
        }, $data);
        $binary = implode("", $data);

        /* Split to eight bit chunks. */
        $data = str_split($binary, 8);

        /* Make sure binary is divisible by eight by ignoring the incomplete byte. */
        $last = array_pop($data);
        if ((null !== $last) && (8 === strlen($last))) {
            $data[] = $last;
        }

        return implode("", array_map(function ($byte) {
            return chr(bindec($byte));
        }, $data));
    }

    /**
     * Encode given integer to a base32 string
     */
    public function encodeInteger(int $data): string
    {
        /* Create binary string zeropadded to eight bits. */
        $binary = decbin($data);
        if ($modulus = strlen($binary) % 5) {
            $padding = 5 - $modulus;
            $binary = str_pad($binary, strlen($binary) + $padding, "0", STR_PAD_LEFT);
        }

        /* Split to five bit chunks and make sure last chunk has five bits. */
        $binary = str_split($binary, 5);
        $last = array_pop($binary);
        if ($last) {
            $binary[] = str_pad($last, 5, "0", STR_PAD_RIGHT);
        }

        /* Convert each five bits to Base32 character. */
        $encoded = implode("", array_map(function ($fivebits) {
            $index = bindec($fivebits);
            return $this->characters()[$index];
        }, $binary));

        /* Pad to eight characters when requested. */
        if (!empty($this->padding())) {
            if ($modulus = strlen($encoded) % 8) {
                $padding = 8 - $modulus;
                $encoded .= str_repeat($this->padding(), $padding);
            }
        }

        return $encoded;
    }

    /**
     * Decode given base32 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                "Cannot decode empty string as integer"
            );
        }

        if ($this->isCrockford()) {
            $data = strtoupper($data);
            $data = str_replace(["O", "L", "I", "-"], ["0", "1", "1", ""], $data);
        }

        /* If the data contains characters that aren't in the character set. */
        $characters = $this->characters() . $this->padding();
        if (strlen($data) !== strspn($data, $characters)) {
            $valid = str_split($this->characters());
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);
            throw new InvalidArgumentException(
                "Data contains invalid characters \"{$invalid}\""
            );
        }

        $data = str_split($data);
        $data = array_map(function ($character) {
            if ($character !== $this->padding()) {
                $index = strpos($this->characters(), $character);
                return sprintf("%05b", $index);
            }
        }, $data);
        $binary = implode("", $data);

        return bindec($binary);
    }
}
