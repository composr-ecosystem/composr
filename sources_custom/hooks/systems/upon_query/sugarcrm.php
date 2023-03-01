<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_upon_query_sugarcrm
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if (!function_exists('curl_init')) {
            return;
        }

        if ($query[0] == 'S') {
            return;
        }

        if (!isset($GLOBALS['FORUM_DB'])) {
            return;
        }

        if (running_script('install')) {
            return;
        }

        if (strpos($query, 'f_member') === false) {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        $prefix = preg_quote($GLOBALS['FORUM_DB']->get_table_prefix(), '#');

        $matches = [];
        if (preg_match('#^INSERT INTO ' . $prefix . 'f_member_custom_fields .*\((\d+),#U', $query, $matches) != 0) {
            if (!addon_installed('sugarcrm')) {
                return;
            }

            require_code('sugarcrm');

            if (!sugarcrm_configured()) {
                return;
            }

            require_code('tasks');
            $_title = do_lang('SUGARCRM_MEMBER_SYNC');
            call_user_func_array__long_task($_title, null, 'sugarcrm_sync_member', [intval($matches[1]), $_GET, $_POST], false, false, false);

            return;
        }
    }
}
