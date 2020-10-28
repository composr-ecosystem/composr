<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2020 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Atom;

/**
 * An atom, which represents a content entry on a provider.
 * Roughly follows the conventions of the Atom data format (https://tools.ietf.org/html/rfc4287).
 */
class Atom
{
    /**
     * Identifier.
     * Null means auto-set for a new one, no existing one will be null.
     *
     * @var ?string
     */
    public $identifier;

    /**
     * Whether this is an incomplete object.
     * False means getAtomFull must be called to get full content.
     *
     * @var bool
     */
    public $isIncomplete = true;

    /**
     * Author.
     *
     * @var ?Author
     */
    public $author;

    /**
     * List of category identifiers.
     *
     * @var array
     */
    public $categories = [];

    /**
     * Publish date.
     *
     * @var DateTime
     */
    public $published;

    /**
     * Modification date.
     *
     * @var ?DateTime
     */
    public $updated;

    /**
     * Title.
     * In plain text.
     * Assumed less that 256 characters long (if we are choosing whether to make a single text result title or content).
     *
     * @var ?string
     */
    public $title;

    /**
     * Summary.
     * In HTML.
     *
     * @var ?string
     */
    public $summary;

    /**
     * Content.
     * In HTML.
     *
     * @var ?string
     */
    public $content;

    /**
     * List of Enclosure objects.
     *
     * @var array
     */
    public $enclosures = [];

    /**
     * Perma-link URL.
     *
     * @var string
     */
    public $url;
}
