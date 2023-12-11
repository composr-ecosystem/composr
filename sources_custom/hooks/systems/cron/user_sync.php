<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_sync
 */

/**
 * Hook class.
 */
class Hook_cron_user_sync
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (get_value('user_sync_enabled') === '1') {
            $_last_time = get_value('last_cron_user_sync', null, true);
            $last_time = is_null($_last_time) ? mixed() : intval($_last_time);
            if (!is_null($last_time)) {
                if ((time() - $last_time) < 60 * 60 * 24) {
                    return;
                }
            }

            $time = time();
            set_value('last_cron_user_sync', strval($time), true);

            require_code('user_sync');

            user_sync__inbound($last_time);
        }
    }
}
