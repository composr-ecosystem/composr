<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_cron_sugarcrm_sync_contact_metadata
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
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        if (get_option('sugarcrm_contact_metadata_field') == '') {
            // Not configured
            return null;
        }

        return array(
            'label' => 'Send SugarCRM contact metadata',
            'num_queued' => null,
            'minutes_between_runs' => 60 * 24,
        );
    }

    /**
     * Run function for Cron hooks. Searches for tasks to perform.
     */
    public function run()
    {
        require_lang('sugarcrm');
        require_code('tasks');
        $_title = do_lang('SUGARCRM_MEMBER_SYNC');
        call_user_func_array__long_task($_title, null, 'sugarcrm_sync_contact_metadata', array(), false, false, false);
    }
}
