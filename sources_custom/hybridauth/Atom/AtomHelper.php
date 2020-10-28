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
}
