<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

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
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  boolean $calculate_num_queued Calculate the number of items queued, if possible
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info($last_run, $calculate_num_queued)
    {
        if (!addon_installed('composr_homesite')) {
            return null;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return null;
        }

        if ($calculate_num_queued) {
            require_code('composr_homesite');

            global $SITE_INFO;
            $num_queued = count(find_expired_sites()) + (isset($SITE_INFO['mysql_root_password']) ? 1 : 0);
        } else {
            $num_queued = null;
        }

        return [
            'label' => 'Reset personal demos',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 60 * 12,
        ];
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run($last_run)
    {
        require_lang('sites');
        demonstratr_delete_old_sites();

        // Reset main demo
        global $SITE_INFO;
        if (!isset($SITE_INFO['mysql_root_password'])) {
            return;
        }
        server__public__demo_reset();
    }
}
