<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    user_sync
 */

/**
 * Hook class.
 */
class Hook_cron_user_sync
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
        if (!addon_installed('user_sync')) {
            return null;
        }

        if (!addon_installed('commandr')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        if (get_value('user_sync_enabled') !== '1') {
            return null;
        }

        return [
            'label' => 'User synchronisation',
            'num_queued' => null,
            'minutes_between_runs' => 60 * 24,
            'enabled_by_default' => true,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_code('user_sync');

        user_sync__inbound($last_run);
    }
}
