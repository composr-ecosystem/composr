<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_simple_spreadsheet_sync
 */

/**
 * Hook class.
 */
class Hook_upon_query_user_export
{
    public $member_rows_old = [];

    public function run_pre($ob, $query, $max, $start, $fail_ok, $get_insert_id)
    {
        if (!addon_installed('user_simple_spreadsheet_sync')) {
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
        if (
            (preg_match('#^UPDATE ' . $prefix . 'f_members .*WHERE \(?id=(\d+)\)?#', $query, $matches) != 0) ||
            (preg_match('#^UPDATE ' . $prefix . 'f_member_custom_fields .*WHERE \(?mf_member_id=(\d+)\)?#', $query, $matches) != 0)
        ) {
            if (strpos($query, 'm_email_address') !== false) {
                $member_id = intval($matches[1]);
                $this->member_rows_old[$member_id] = $GLOBALS['FORUM_DRIVER']->get_member_row($member_id);
            }
            return;
        }

        $matches = [];
        if (
        (preg_match('#^DELETE FROM ' . $prefix . 'f_members .*WHERE id=(\d+)#', $query, $matches) != 0)
        ) {
            require_code('user_export');
            do_user_export__single_ipc(intval($matches[1]), true);
            return;
        }
    }

    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
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
        if (
            (preg_match('#^UPDATE ' . $prefix . 'f_members .*WHERE \(?id=(\d+)\)?#', $query, $matches) != 0) ||
            (preg_match('#^UPDATE ' . $prefix . 'f_member_custom_fields .*WHERE \(?mf_member_id=(\d+)\)?#', $query, $matches) != 0)
        ) {
            if (!addon_installed('user_simple_spreadsheet_sync')) {
                return;
            }

            if (strpos($query, 'm_email_address') !== false) {
                $member_id = intval($matches[1]);

                if (strpos($query, '\'' . db_escape_string($this->member_rows_old[$member_id]['m_email_address']) . '\'') === false) {
                    require_code('user_export');
                    do_user_export__single_ipc($member_id);
                }
            }
            return;
        }

        /*$matches = [];   Can cause loop, use the below code
        if (
        (preg_match('#^INSERT INTO ' . $prefix . 'f_members #', $query, $matches) != 0)
        ) {
            require_code('user_export');
            do_user_export__single_ipc($ret);
            return;
        }*/

        $matches = [];
        if (
        (preg_match('#^INSERT INTO ' . $prefix . 'f_member_custom_fields .*\((\d+),#U', $query, $matches) != 0)
        ) {
            require_code('user_export');
            do_user_export__single_ipc(intval($matches[1]));
            return;
        }
    }
}
