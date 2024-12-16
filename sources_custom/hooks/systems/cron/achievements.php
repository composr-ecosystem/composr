<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

/**
 * Hook class.
 */
class Hook_cron_achievements
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
        if (!addon_installed('achievements')) {
            return null;
        }

        // If we want to know the queue count, then let's use "1" if this hook will actually run (XML is valid)
        $num_queued = null;
        if ($calculate_num_queued) {
            require_code('achievements');
            $ob = load_achievements();
            if ($ob->is_xml_valid() === true) {
                $num_queued = 1;
            } else {
                $num_queued = 0;
            }
        }

        return [
            'label' => 'Check achievements on random members',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 1,
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
        // Initialize achievements
        require_code('achievements');
        $ob = load_achievements(true);

        // Run a cleanup if we detected the XML file was modified since last run (in case it was done so manually instead of through the UI)
        if (($last_run === null) || (filemtime(get_custom_file_base() . '/data_custom/xml_config/achievements.xml') >= $last_run)) {
            $ob->cleanup();
        }

        if ($ob->is_xml_valid() === false) {
            return; // For safety, do not run re-calculations if any XML issues are present in the achievements system
        }

        // Get 100 random members
        $max = 100;
        $members_to_do = $GLOBALS['FORUM_DB']->query_select('f_members', ['id'], [], ' AND id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' ORDER BY ' . db_function('RAND'), $max);

        // Run re-calculations on the members randomly chosen
        $time_start = microtime(true);
        foreach ($members_to_do as $member) {
            $ob->recalculate_achievement_progress($member['id']);

            // Don't process any more members once we've spent 5 or more seconds on this hook
            if ((microtime(true) - $time_start) >= 5.0) {
                break;
            }
        }
    }
}
