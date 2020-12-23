<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        $cat_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'id', ['c_name' => 'community_sites']);

        return strval($cat_id);
    }
}
