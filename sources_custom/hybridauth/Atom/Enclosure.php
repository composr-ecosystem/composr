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
    /**
     * An ENCLOSURE_* constant from AtomHelper.
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
}
