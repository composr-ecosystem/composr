<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    content_read_tracking
 */

/**
 * Hook class.
 */
class Hook_symbol_HAS_READ
{
    public function run($param)
    {
        if (!isset($param[1])) {
            return '1'; // Not enough parameters
        }
        if (is_guest()) {
            return '1'; // Guests can't be tracked, assume read (so no unread icon might show, which is the normal use-case for this feature)
        }
        if (!$GLOBALS['SITE_DB']->table_exists('content_read')) {
            return '0'; // Not installed yet
        }

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('content_read', 'r_time', array(
            'r_content_type' => $param[0],
            'r_content_id' => $param[1],
            'r_member_id' => get_member()
        ));

        if (!is_null($test)) {
            return '1';
        }

        // First check this isn't really old content that we no longer track
        if (isset($param[3])) {
            $cleanup_days = intval($param[2]);
            $content_time = intval($param[3]);
            if ($content_time < time() - 60 * 60 * 24 * $cleanup_days) {
                return '1'; // Content too old, not tracking, assume read
            }
        }

        return '0';
    }
}
