<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    data_mappr
 */

/**
 * Hook class.
 */
class Hook_upon_query_google_maps
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if ($query[0] == 'S') {
            return;
        }

        if ((strpos($query, 'main_cc_embed') !== false) && (preg_match('#^DELETE FROM ' . get_table_prefix() . 'cache WHERE .*main_cc_embed#', $query) != 0)) { // If main_cc_embed being decached
            decache('main_google_map'); // decache map block too
        }
    }
}
