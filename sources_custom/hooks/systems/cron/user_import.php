<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class Hook_cron_user_import
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        require_code('user_import');

        if (!USER_IMPORT_ENABLED) {
            return;
        }

        $last = get_value('last_user_import');
        if ((is_null($last)) || (intval($last) < time() - 60 * USER_IMPORT_MINUTES)) {
            set_value('last_user_import', strval(time()));

            do_user_import();
        }
    }
}
