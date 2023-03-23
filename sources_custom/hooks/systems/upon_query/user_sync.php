<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_sync
 */

/**
 * Hook class.
 */
class Hook_upon_query_user_sync
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if (get_forum_type() != 'cns') {
            return null;
        }

        if ($query[0] == 'S') {
            return;
        }

        if (!function_exists('get_value')) {
            return; // Installer?
        }

        if (!$GLOBALS['VALUES_FULLY_LOADED']) {
            return;
        }

        if (strpos($query, 'f_member') === false) {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        if (get_value('user_sync_enabled') === '1') {
            $prefix = preg_quote($GLOBALS['FORUM_DB']->get_table_prefix(), '#');

            $matches = [];
            if (
                (preg_match('#^UPDATE ' . $prefix . 'f_members .*WHERE \(?id=(\d+)\)?#', $query, $matches) != 0) ||
                (preg_match('#^UPDATE ' . $prefix . 'f_member_custom_fields .*WHERE \(?mf_member_id=(\d+)\)?#', $query, $matches) != 0)
            ) {
                if (!addon_installed('user_sync')) {
                    return;
                }

                if (!addon_installed('commandr')) {
                    return;
                }

                require_code('user_sync');
                user_sync__outbound_edit(intval($matches[1]));
                return;
            }

            $matches = [];
            if ((preg_match('#^DELETE FROM ' . $prefix . 'f_members WHERE \(?id=(\d+)\)?#', $query, $matches) != 0)) {
                if (!addon_installed('user_sync')) {
                    return;
                }

                if (!addon_installed('commandr')) {
                    return;
                }

                require_code('user_sync');
                user_sync__outbound_delete(intval($matches[1]));
                return;
            }

            $matches = [];
            if ((preg_match('#^INSERT INTO ' . $prefix . 'f_members #', $query, $matches) != 0)) {
                if (!addon_installed('user_sync')) {
                    return;
                }

                if (!addon_installed('commandr')) {
                    return;
                }

                require_code('user_sync');
                user_sync__outbound($ret);
                return;
            }

            $matches = [];
            if ((preg_match('#^INSERT INTO ' . $prefix . 'f_member_custom_fields .*\((\d+),#U', $query, $matches) != 0)) {
                if (!addon_installed('user_sync')) {
                    return;
                }

                if (!addon_installed('commandr')) {
                    return;
                }

                require_code('user_sync');
                user_sync__outbound(intval($matches[1]));
                return;
            }
        }
    }
}
