<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
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
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return null;
        }

        // Calculate on low priority
        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        if ($calculate_num_queued) {
            require_code('cms_homesite');

            global $SITE_INFO;
            $num_queued = count(find_expired_sites()) + ((isset($SITE_INFO['mysql_root_password']) && isset($SITE_INFO['mysql_demonstratr_password'])) ? 1 : 0);
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
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_lang('sites');
        demonstratr_delete_old_sites();

        // Reset main demo
        server__public__demo_reset();
    }
}
