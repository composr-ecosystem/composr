<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    group_points
 */

/**
 * Hook class.
 */
class Hook_cron_group_points
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
        if (!addon_installed('group_points')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }

        return [
            'label' => 'Assign points for usergroup membership',
            'num_queued' => null,
            'minutes_between_runs' => 30,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_code('points');

        $actual_last_run = get_value('cron_group_points__last_run', null, true);
        $current_month = date('Y-m', tz_time(time(), get_site_timezone()));

        // Do not run if it is not a new month
        if ($actual_last_run === $current_month) {
            return;
        }

        set_value('cron_group_points__last_run', $current_month, true);

        require_code('points');
        require_code('points2');

        $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true, true);
        $group_points = get_group_points();

        $fields = new Tempcode();

        foreach ($groups as $group_id => $group_name) {
            if (isset($group_points[$group_id])) {
                $points = $group_points[$group_id];
                if ($points['p_points_per_month'] > 0) {
                    $start = 0;
                    do {
                        $members = $GLOBALS['FORUM_DRIVER']->member_group_query([$group_id], 100, $start);
                        foreach ($members as $member_row) {
                            $member_id = $GLOBALS['FORUM_DRIVER']->mrow_member_id($member_row);
                            points_credit_member($member_id, 'Being in the ' . $group_name . ' usergroup', $points['p_points_per_month'], 0, true, 0, 'usergroup', 'group_points', strval($group_id));
                        }
                        $start += 100;
                    } while (!empty($members));
                }
            }
        }
    }
}
