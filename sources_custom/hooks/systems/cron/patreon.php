<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_cron_patreon
{
    protected $adapters;

    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('patreon')) {
            return null;
        }

        if (!addon_installed('hybridauth')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        require_code('patreon');
        $adapters = get_patreon_hybridauth_adapters();

        if (empty($adapters)) {
            return null;
        }

        return [
            'label' => 'Patreon patron sync',
            'num_queued' => null,
            'minutes_between_runs' => 24 * 60,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_code('patreon');
        patreon_sync();
    }
}
