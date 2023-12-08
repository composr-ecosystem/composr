<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_simple_csv_sync
 */

/**
 * Hook class.
 */
class Hook_cron_user_export
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        require_code('user_export');

        if (!USER_EXPORT_ENABLED) {
            return;
        }

        $last = get_value('last_user_export');
        if ((is_null($last)) || (intval($last) < time() - 60 * USER_EXPORT_MINUTES)) {
            set_value('last_user_export', strval(time()));

            do_user_export();
        }
    }
}
