<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_logs_tapatalk
{
    /**
     * Find supported logs.
     *
     * @return array List of logs
     */
    public function enumerate_logs() : array
    {
        if (!addon_installed('cns_tapatalk')) {
            return [
            ];
        }

        return [
            'tapatalk.log' => ['days_to_keep' => (get_option('days_to_keep__tapatalk_log') == '') ? null : intval(get_option('days_to_keep__tapatalk_log'))],
        ];
    }
}
