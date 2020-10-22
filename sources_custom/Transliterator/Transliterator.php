<?php

/**
 * This is the part taken from Doctrine 1.2.3
 * Doctrine inflector has static methods for inflecting text.
 *
 * The methods in these classes are from several different sources collected
 * across several different php projects and several different authors. The
 * original author names and emails are not known
 *
 * Uses 3rd party libraries and functions:
 *         http://sourceforge.net/projects/phputf8
 *
 * @license        http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 * @since          1.0
 *
 * @author         Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author         Jonathan H. Wage <jonwage@gmail.com>
 * @author         <hsivonen@iki.fi>
 */
class BehatTransliterator
{
    /**
     * Tansliterate the UTF-8 string to ASCII.
     *
     * Uses transliteration tables to convert any kind of utf8 character.
     *
     * @param string $text
     *
     * @return string $text
     */
    public static function transliterate($text)
    {
        if (preg_match('/[\x80-\xff]/', $text) && self::validUtf8($text)) {
            $text = self::utf8ToAscii($text);
        }

        return $text;
    }

    /**
     * Tests a string as to whether it's valid UTF-8 and supported by the
     * Unicode standard.
     *
     * Note: this function has been modified to simple return true or false
     *
     * @author <hsivonen@iki.fi>
     *
     * @param string $str UTF-8 encoded string
     *
     * @return bool
     *
     * @see    http://hsivonen.iki.fi/php-utf8/
     */
    public static function validUtf8($str)
    {
        $mState = 0; // cached expected number of octets after the current octet
        // until the beginning of the next UTF8 character sequence
        $mUcs4 = 0; // cached Unicode character
        $mBytes = 1; // cached expected number of octets in the current sequence

        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i) {
            $in = ord($str[$i]);
            if ($mState == 0) {
                // When mState is zero we expect either a US-ASCII character or a
                // multi-octet sequence.
                if (0 == (0x80 & ($in))) {
                    // US-ASCII, pass straight through.
                    $mBytes = 1;
                } elseif (0xC0 == (0xE0 & ($in))) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif (0xE0 == (0xF0 & ($in))) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif (0xF0 == (0xF8 & ($in))) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif (0xF8 == (0xFC & ($in))) {
                    /* First octet of 5 octet sequence.
                    *
                    * This is illegal because the encoded codepoint must be either
                    * (a) not the shortest form or
                    * (b) outside the Unicode range of 0-0x10FFFF.
                    * Rather than trying to resynchronize, we will carry on until the end
                    * of the sequence and let the later error handling code catch it.
                    */
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif (0xFC == (0xFE & ($in))) {
                    // First octet of 6 octet sequence, see comments for 5 octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    /* Current octet is neither in the US-ASCII range nor a legal first
                     * octet of a multi-octet sequence.
                     */
                    return false;
                }
            } else {
                // When mState is non-zero, we expect a continuation of the multi-octet
                // sequence
                if (0x80 == (0xC0 & ($in))) {
                    // Legal continuation.
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;
                    /*
                     * End of the multi-octet sequence. mUcs4 now contains the final
                     * Unicode codepoint to be output
                     */
                    if (0 == --$mState) {
                        /*
                        * Check for illegal sequences and codepoints.
                        */
                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                            ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                            ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                            (4 < $mBytes) ||
                            // From Unicode 3.2, surrogate characters are illegal
                            (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                            // Codepoints outside the Unicode range are illegal
                            ($mUcs4 > 0x10FFFF)
                        ) {
                            return false;
                        }
                        //initialize UTF8 cache
                        $mState = 0;
                        $mUcs4 = 0;
                        $mBytes = 1;
                    }
                } else {
                    /*
                     *((0xC0 & (*in) != 0x80) && (mState != 0))
                     * Incomplete multi-octet sequence.
                     */
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Transliterates an UTF-8 string to ASCII.
     *
     * US-ASCII transliterations of Unicode text
     * Ported Sean M. Burke's Text::Unidecode Perl module (He did all the hard work!)
     * Warning: you should only pass this well formed UTF-8!
     * Be aware it works by making a copy of the input string which it appends transliterated
     * characters to - it uses a PHP output buffer to do this - it means, memory use will increase,
     * requiring up to the same amount again as the input string.
     *
     * @see http://search.cpan.org/~sburke/Text-Unidecode-0.04/lib/Text/Unidecode.pm
     *
     * @author <hsivonen@iki.fi>
     *
     * @param string $str     UTF-8 string to convert
     * @param string $unknown Character use if character unknown (default to ?)
     *
     * @return string US-ASCII string
     */
    public static function utf8ToAscii($str, $unknown = '?')
    {
        static $UTF8_TO_ASCII;

        if (strlen($str) == 0) {
            return '';
        }

        preg_match_all('/.{1}|[^\x00]{1,1}$/us', $str, $ar);
        $chars = $ar[0];

        foreach ($chars as $i => $c) {
            if (ord($c[0]) >= 0 && ord($c[0]) <= 127) {
                continue;
            } // ASCII - next please
            if (ord($c[0]) >= 192 && ord($c[0]) <= 223) {
                $ord = (ord($c[0]) - 192) * 64 + (ord($c[1]) - 128);
            }
            if (ord($c[0]) >= 224 && ord($c[0]) <= 239) {
                $ord = (ord($c[0]) - 224) * 4096 + (ord($c[1]) - 128) * 64 + (ord($c[2]) - 128);
            }
            if (ord($c[0]) >= 240 && ord($c[0]) <= 247) {
                $ord = (ord($c[0]) - 240) * 262144 + (ord($c[1]) - 128) * 4096 + (ord($c[2]) - 128) * 64 + (ord($c[3]) - 128);
            }
            if (ord($c[0]) >= 248 && ord($c[0]) <= 251) {
                $ord = (ord($c[0]) - 248) * 16777216 + (ord($c[1]) - 128) * 262144 + (ord($c[2]) - 128) * 4096 + (ord($c[3]) - 128) * 64 + (ord($c[4]) - 128);
            }
            if (ord($c[0]) >= 252 && ord($c[0]) <= 253) {
                $ord = (ord($c[0]) - 252) * 1073741824 + (ord($c[1]) - 128) * 16777216 + (ord($c[2]) - 128) * 262144 + (ord($c[3]) - 128) * 4096 + (ord($c[4]) - 128) * 64 + (ord($c[5]) - 128);
            }
            if (ord($c[0]) >= 254 && ord($c[0]) <= 255) {
                $chars[$i] = $unknown;
                continue;
            } //error

            $bank = $ord >> 8;

            if (!array_key_exists($bank, (array) $UTF8_TO_ASCII)) {
                $bankfile = __DIR__.'/data/'.sprintf('x%02x', $bank).'.php';
                if (file_exists($bankfile)) {
                    include $bankfile;
                } else {
                    $UTF8_TO_ASCII[$bank] = array();
                }
            }

            $newchar = $ord & 255;
            if (array_key_exists($newchar, $UTF8_TO_ASCII[$bank])) {
                $chars[$i] = $UTF8_TO_ASCII[$bank][$newchar];
            } else {
                $chars[$i] = $unknown;
            }
        }

        return implode('', $chars);
    }
}
