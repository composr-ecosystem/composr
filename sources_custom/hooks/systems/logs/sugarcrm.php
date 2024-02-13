<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_logs_sugarcrm
{
    /**
     * Find supported logs.
     *
     * @return array List of logs
     */
    public function enumerate_logs() : array
    {
        if (!addon_installed('sugarcrm')) {
            return [
            ];
        }

        return [
            'sugarcrm.log' => ['days_to_keep' => (get_option('days_to_keep__sugarcrm_log') == '') ? null : intval(get_option('days_to_keep__sugarcrm_log'))],
        ];
    }
}
