<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_upon_query_tapatalk_push
{
    public function run($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if (!addon_installed('cns_tapatalk')) {
            return;
        }

        if (!addon_installed('cns_forum')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        if ($query[0] == 'S') {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        if ((strpos($query, 'INTO ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts ') !== false) && ($get_insert_id)) {
            require_once(get_file_base() . '/mobiquo/lib/TapatalkPush.php');
            $push = new TapatalkPush();
            cms_register_shutdown_function_safe([$push, 'do_push'], $ret);
        }

        if (strpos($query, 'INTO ' . get_table_prefix() . 'rating ') !== false) {
            $matches = [];
            if (preg_match('#\(rating_for_type, rating_for_id,.*\) VALUES \(\'post\', \'(\d+)\',.*, 10\)#', $query, $matches) != 0) {
                require_once(get_file_base() . '/mobiquo/lib/TapatalkPush.php');
                $push = new TapatalkPush();
                cms_register_shutdown_function_safe([$push, 'do_like_push'], intval($matches[1]));
            }
        }
    }
}
