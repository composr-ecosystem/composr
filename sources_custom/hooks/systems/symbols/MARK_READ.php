<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    content_read_tracking
 */

/**
 * Hook class.
 */
class Hook_symbol_MARK_READ
{
    public function run($param)
    {
        if (!addon_installed('content_read_tracking')) {
            return '';
        }

        if ((empty($param[0])) || (!isset($param[1]))) {
            return ''; // Not enough parameters
        }
        if (is_guest()) {
            return ''; // Guests can't be tracked
        }

        $GLOBALS['SITE_DB']->query_insert('content_read', [
            'r_content_type' => $param[0],
            'r_content_id' => $param[1],
            'r_member_id' => get_member(),
            'r_time' => time(),
        ], false, true);

        // Cleanup stale data
        $cleanup_days = (!empty($param[2])) ? intval($param[2]) : 0;
        if (($cleanup_days != 0) && (mt_rand(0, 100) == 1/*Only do 1% of the time, for performance reasons*/)) {
            cms_register_shutdown_function_safe(function () use ($cleanup_days) {
                if (!$GLOBALS['SITE_DB']->table_is_locked('content_read')) {
                    $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'content_read WHERE r_time<' . strval(time() - 60 * 60 * 24 * $cleanup_days), 500/*to reduce lock times*/, 0, true); // Errors suppressed in case DB write access broken
                }
            });
        }

        return ''; // Not output for this symbol ever
    }
}
