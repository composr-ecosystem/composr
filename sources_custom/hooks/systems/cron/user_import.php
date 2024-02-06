<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_simple_spreadsheet_sync
 */

/**
 * Hook class.
 */
class Hook_cron_user_import
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
        if (!addon_installed('user_simple_spreadsheet_sync')) {
            return null;
        }

        require_code('user_import');

        if (!USER_IMPORT_ENABLED) {
            return null;
        }

        return [
            'label' => 'User import',
            'num_queued' => null,
            'minutes_between_runs' => USER_IMPORT_MINUTES,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        do_user_import();
    }
}
