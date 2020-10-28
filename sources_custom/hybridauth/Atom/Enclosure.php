<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2020 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Atom;

/**
 * An enclosure (a file)
 */
class Enclosure
{
    const ENCLOSURE_IMAGE = 1;
    const ENCLOSURE_VIDEO = 2;
    const ENCLOSURE_BINARY = 4;

    /**
     * An ENCLOSURE_* constant.
     *
     * @var int
     */
    public $type;

    /**
     * Mime-type.
     *
     * @var ?string
     */
    public $mimeType;

    /**
     * Content length.
     *
     * @var ?int
     */
    public $contentLength;

    /**
     * URL. Ideally to the raw file, but if necessary to the web page that views it.
     *
     * @var string
     */
    public $url;

    /**
     * Thumbnail URL, to a raw image file.
     *
     * @var ?string
     */
    public $thumbnailUrl;

    /**
     * Guess a mime-type based on file extension.
     *
     * @param string $url URL
     *
     * @return ?string Mime type or null
     */
    public static function guessMimeType($url)
    {
        if (preg_match('#\.(jpg|jpeg)($|\?)#', $url) != 0) {
            return 'image/jpeg';
        } elseif (preg_match('#\.(png)($|\?)#', $url) != 0) {
            return 'image/png';
        }
        return null;
    }
}
