<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2020 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Atom;

/**
 * Constants and helper functions for working with the atom API.
 */
class AtomHelper
{
    /**
     * Convert HTML to plain text.
     *
     * @param string $html
     *
     * @return string
     */
    public static function htmlToPlainText($html)
    {
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_XML1, 'utf-8');
        return str_replace('<br />', "\n", preg_replace('#\s+#', ' ', $decoded));
    }

    /**
     * Convert plain text to HTML.
     *
     * @param string $text
     *
     * @return string
     */
    public static function plainTextToHtml($text)
    {
        return nl2br(htmlentities($text, ENT_QUOTES | ENT_XML1, 'utf-8'));
    }

    /**
     * Convert special codes within text to HTML.
     * Assumes plainTextToHtml-style conversion has already happened.
     *
     * @param string $text
     * @param ?string $urlUsernames Regexp-replacement-value for replacing usernames, or null
     * @param ?string $urlHashtags Regexp-replacement-value for replacing hashtags, or null
     * @param bool $detectUrls Convert raw URLs to hyperlinks
     *
     * @return array A pair: string of new text, and whether a replacement happened
     */
    public static function processCodes($text, $urlUsernames, $urlHashtags, $detectUrls = false)
    {
        $textIn = $text;
        if ($urlUsernames !== null) {
            $text = preg_replace('/@((\w|\.)+)/', $urlUsernames, $text); // users
        }
        if ($urlHashtags !== null) {
            $text = preg_replace('/\s#(\w+)/', ' ' . $urlHashtags, $text); // hashtags
        }
        if ($detectUrls) {
            $urlRegexp = '#([^"\'])(https?://([\w\-\.]+)+(/([\w/_\.]*(\?\S+)?(\#\S+)?)?)?)#';
            $text = preg_replace($urlRegexp, '$1<a href="$2">$2</a>', $text); // links
        }
        return [$text, $text != $textIn];
    }

    /**
     * Get string length, with utf-8 awareness.
     *
     * @param string $in The string to get the length of
     * @return integer The string length
     */
    public static function mbStrlen($in)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($in, 'utf-8');
        }
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($in, 'utf-8');
        }
        return strlen($in);
    }

    /**
     * Return part of a string, with utf-8 awareness.
     *
     * @param  string $in The subject
     * @param  integer $from The start position
     * @param  ?integer $amount The length to extract (null: all remaining)
     * @return ~string String part (false: $start was over the end of the string)
     */
    public static function mbSubstr($in, $from, $amount = null)
    {
        if ($amount === null) {
            $amount = self::mbStrlen($in, $force) - $from;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($in, $from, $amount, 'utf-8');
        }
        if (function_exists('iconv_substr')) {
            return iconv_substr($in, $from, $amount, 'utf-8');
        }
        return substr($in, $from, $amount);
    }
}
