<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    banner_click_points
 */

/**
 * Hook class.
 */
class Hook_upon_query_banner_click_points
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if ($query[0] == 'S') {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        if (strpos($query, 'INTO ' . get_table_prefix() . 'banner_clicks') !== false) {
            load_user_stuff();
            if (method_exists($GLOBALS['FORUM_DRIVER'], 'forum_layer_initialise')) {
                $GLOBALS['FORUM_DRIVER']->forum_layer_initialise();
            }
            global $FORCE_INVISIBLE_GUEST, $MEMBER_CACHED;
            $FORCE_INVISIBLE_GUEST = false;
            $MEMBER_CACHED = null;

            if (!is_guest()) {
                require_code('comcode');
                require_code('permissions');

                $member_id = get_member();

                $dest = get_param_string('dest', '');

                $cnt = $GLOBALS['SITE_DB']->query_select_value('banner_clicks', 'COUNT(*)', array(
                    'c_member_id' => $member_id,
                    'c_banner_id' => $dest,
                ));
                if ($cnt == 0) {
                    require_code('points');
                    require_code('points2');
                    system_gift_transfer('Clicking a banner', 1, $member_id);
                }
            }
        }
    }
}
