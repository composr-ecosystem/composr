<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_mappr
 */

/**
 * Hook class.
 */
class Hook_upon_query_google_maps_users
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if ($query[0] == 'S') {
            return;
        }

        if ((strpos($query, 'f_member_custom_fields') !== false) && ((strpos($query, 'INSERT INTO ') !== false) || (strpos($query, 'UPDATE ') !== false))) {
            if (!addon_installed('user_mappr')) {
                return;
            }

            if (function_exists('delete_cache_entry')) {
                delete_cache_entry('main_google_map_users');
            }
        }
    }
}
