<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_cron_site_cleanup
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        require_lang('sites');
        require_code('composr_homesite');
        demonstratr_delete_old_sites();

        // Reset demo
        $last = get_value('last_demo_set_time');
        if ((is_null($last)) || (intval($last) < time() - 60 * 60 * 12)) {
            set_value('last_demo_set_time', strval(time()));

            global $SITE_INFO;
            if (!isset($SITE_INFO['mysql_root_password'])) {
                return;
            }

            server__public__demo_reset();
        }
    }
}
