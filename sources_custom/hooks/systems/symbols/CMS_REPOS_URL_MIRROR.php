<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_symbol_CMS_REPOS_URL_MIRROR
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('cms_homesite')) {
            return '';
        }

        require_code('version');
        return CMS_REPOS_URL_MIRROR;
    }
}
