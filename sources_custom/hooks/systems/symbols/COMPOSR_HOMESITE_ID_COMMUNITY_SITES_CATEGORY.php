<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_symbol_COMPOSR_HOMESITE_ID_COMMUNITY_SITES_CATEGORY
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run($param)
    {
        if (!addon_installed('composr_homesite')) {
            return '';
        }
        if (!addon_installed('catalogues')) {
            return '';
        }

        $private = !empty($param[0]);
        $cat_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', $private ? 'MAX(id)' : 'MIN(id)', ['c_name' => 'community_sites']);

        return strval($cat_id);
    }
}
