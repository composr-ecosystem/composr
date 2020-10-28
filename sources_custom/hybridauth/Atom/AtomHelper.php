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
    const ENCLOSURE_IMAGE = 1;
    const ENCLOSURE_VIDEO = 2;
    const ENCLOSURE_BINARY = 4;

    /**
     * Convert HTML to plain text.
     *
     * @param string $html
     *
     * @return string
     */
    public static function htmlToPlainText($html)
    {
        return str_replace('<br />', "\n", preg_replace('#\s+#', ' ', html_entity_decode($html)));
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
        return nl2br(htmlentities($text));
    }
}
