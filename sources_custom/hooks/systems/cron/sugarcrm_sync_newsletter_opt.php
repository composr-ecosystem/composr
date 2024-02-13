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
class Hook_cron_sugarcrm_sync_newsletter_opt
{
    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        if ($calculate_num_queued) {
            $num_queued = $GLOBALS['SITE_DB']->query_select_value_if_there('mail_opt_sync_queue', 'COUNT(*)', [], ' AND processed_time IS NULL');
        } else {
            $num_queued = null;
        }

        return [
            'label' => 'Send SugarCRM newsletter opt requests',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 1,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        // Cron is locked
        if (get_value('sugarcrm_opt_sync_lock', '0', true) == '1') {
            require_lang('sugarcrm');
            warn_exit(do_lang_tempcode('SUGARCRM_NEWSLETTER_OPT_SYNC_LOCKED'));
            return null;
        }

        // Check if there is anything to sync
        $num_queued = $GLOBALS['SITE_DB']->query_select_value_if_there('mail_opt_sync_queue', 'COUNT(*)', [], ' AND processed_time IS NULL');
        if ($num_queued == 0) {
            return null;
        }

        $error_count = -1; // -1 forces a lock-out and error notification if a connection cannot be established.
        require_code('sugarcrm');
        try {
            $success = sugarcrm_initialise_connection();
            if (!$success) {
                _sugarcrm_opt_sync_error($error_count);
                return false;
            }
        } catch (Exception $e) {
            _sugarcrm_opt_sync_error($error_count);
            return false;
        }

        sync_newsletter_opt_into_sugarcrm();

        return null;
    }
}
