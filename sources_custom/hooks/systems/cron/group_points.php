<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * @param  boolean $calculate_num_queued Calculate the number of items queued, if possible
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info($last_run, $calculate_num_queued)
    {
        if (!addon_installed('group_points')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }

        return array(
            'label' => 'Assign points for usergroup membership',
            'num_queued' => null,
            'minutes_between_runs' => 60 * 24 * 27, // Only once within a month
        );
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run($last_run)
    {
        require_code('points');

        if (date('j') != '1') {
            return; // Only on first day
        }

        require_code('points');
        require_code('points2');

        $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true, true);
        $group_points = get_group_points();

        $fields = new Tempcode();

        foreach ($groups as $group_id => $group_name) {
            if (isset($group_points[$group_id])) {
                $points = $group_points[$group_id];
                if ($points['p_points_per_month'] != 0) {
                    $start = 0;
                    do {
                        $members = $GLOBALS['FORUM_DRIVER']->member_group_query(array($group_id), 100, $start);
                        foreach ($members as $member_row) {
                            $member_id = $GLOBALS['FORUM_DRIVER']->mrow_id($member_row);
                            system_gift_transfer('Being in the ' . $group_name . ' usergroup', $points['p_points_per_month'], $member_id);
                        }
                        $start += 100;
                    } while (!empty($members));
                }
            }
        }
    }
}
