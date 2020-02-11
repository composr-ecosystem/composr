<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

/**
 * Hook class.
 */
class Hook_cron_gallery_syndication
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
        if (!addon_installed('gallery_syndication')) {
            return null;
        }

        if (!addon_installed('galleries')) {
            return null;
        }

        if (!function_exists('curl_init')) {
            return null;
        }

        return [
            'label' => 'Synchronise galleries',
            'num_queued' => null, // Too time-consuming to calculate
            'minutes_between_runs' => 60,
        ];
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run($last_run)
    {
        require_code('gallery_syndication');

        // Main sync of everything
        sync_video_syndication();
    }
}
