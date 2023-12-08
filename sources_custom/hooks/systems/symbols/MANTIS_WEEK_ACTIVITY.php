<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_symbol_MANTIS_WEEK_ACTIVITY
{
    public function run($param)
    {
        $cnt_in_last_week = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM mantis_bug_table WHERE last_updated>' . strval(time() - 60 * 60 * 24 * 7));
        return strval($cnt_in_last_week);
    }
}
