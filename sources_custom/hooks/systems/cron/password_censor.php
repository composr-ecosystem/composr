<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_cron_password_censor
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        $last = get_value('last_password_censor_time');
        if ((is_null($last)) || (intval($last) < time() - 60 * 60 * 12)) {
            set_value('last_password_censor_time', strval(time()));

            require_code('password_censor');
            password_censor(true, false);
        }
    }
}
