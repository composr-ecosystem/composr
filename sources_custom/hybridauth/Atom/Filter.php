<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2020 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Atom;

/**
 * A filter.
 */
class Filter
{
    /**
     * The ID of a category.
     *
     * @var string
     */
    public $categoryFilter = null;

    /**
     * A bitmask of Enclosure::ENCLOSURE_* constants.
     *
     * @var int
     */
    public $enclosureTypeFilter = null;

    /**
     * Include content 3rd-parties have contributed, if applicable.
     *
     * @var boolean
     */
    public $includeContributedContent = false;

    public function passesEnclosureTest($enclosures)
    {
        if ($this->enclosureTypeFilter === null) {
            return true;
        }
        foreach ($enclosures as $enclosure) {
            if (($this->enclosureTypeFilter & $enclosure->type) != 0) {
                return true;
            }
        }
        return false;
    }
}
