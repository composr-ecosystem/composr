<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    stealr
 */

/**
 * Hook class.
 */
class Hook_cron_stealr
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
        if (!addon_installed('stealr')) {
            return null;
        }
        if (!addon_installed('points')) {
            return null;
        }
        if (!addon_installed('ecommerce')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        $stealr_group = get_option('stealr_group');
        if ($stealr_group == '') {
            return null;
        }

        return [
            'label' => 'Stealr Steal points',
            'num_queued' => null,
            'minutes_between_runs' => 60 * 7 * 24,
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
        require_lang('stealr');

        $type = get_option('stealr_type', true);
        $type = (empty($type)) ? 'Members that are inactive, but has lots points' : $type;

        $_victim_count = get_option('stealr_number', true);
        $victim_count = empty($_victim_count) ? 1 : intval($_victim_count);

        $_points_to_steal = get_option('stealr_points', true);
        $points_to_steal = empty($_points_to_steal) ? 10 : intval($_points_to_steal);

        // Start determining the various cases
        switch ($type) {
            case 'Members that are inactive, but has lots points':
                $all_members = $GLOBALS['FORUM_DRIVER']->get_top_posters(1000); // Top 1000 is how we define "lots points"
                $members_points = [];
                foreach ($all_members as $member) {
                    $id = $GLOBALS['FORUM_DRIVER']->mrow_member_id($member);
                    $signin_time = $member['m_last_visit_time'];
                    $members_points[$signin_time] = ['points' => points_balance($id), 'id' => $id];
                }
                ksort($members_points);

                $victim_count = (count($members_points) > $victim_count) ? $victim_count : count($members_points);

                $theft_count = 0;
                foreach ($members_points as $member) {
                    $theft_count++;
                    if ($theft_count > $victim_count) {
                        break;
                    }

                    $victim_member_id = $member['id'];
                    $victor_member_id = $this->pick_victor($victim_member_id);

                    $total_points = $member['points'];

                    $this->do_point_transfer(min($total_points, $points_to_steal), $victim_member_id, $victor_member_id);
                }

                break;

            case 'Members that are rich':
                $all_members = $GLOBALS['FORUM_DRIVER']->get_top_posters(100);
                $members_points = [];
                foreach ($all_members as $member) {
                    $id = $GLOBALS['FORUM_DRIVER']->mrow_member_id($member);
                    $members_points[$id] = points_balance($id);
                }
                arsort($members_points);

                $victim_count = (count($members_points) > $victim_count) ? $victim_count : count($members_points);

                $theft_count = 0;
                foreach ($members_points as $victim_member_id => $av_points) {
                    $theft_count++;
                    if ($theft_count > $victim_count) {
                        break;
                    }

                    $victor_member_id = $this->pick_victor($victim_member_id);

                    $total_points = $av_points;

                    $this->do_point_transfer(min($total_points, $points_to_steal), $victim_member_id, $victor_member_id);
                }

                break;

            case 'Members that are random':
                $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                if (addon_installed('validation')) {
                    $sql .= ' AND m_validated=1';
                }
                $sql .= ' ORDER BY ' . db_function('RAND');
                $random_members = $GLOBALS['FORUM_DB']->query($sql, $victim_count);

                $victim_count = (count($random_members) > $victim_count) ? $victim_count : count($random_members);

                foreach ($random_members as $member) {
                    $victim_member_id = $member['id'];
                    $victor_member_id = $this->pick_victor($victim_member_id);

                    $total_points = points_balance($victim_member_id);

                    $this->do_point_transfer(min($total_points, $points_to_steal), $victim_member_id, $victor_member_id);
                }

                break;

            case 'Members that are in a certain usergroup':
                $stealr_group = get_option('stealr_group', true);
                $stealr_group = empty($stealr_group) ? 'Member' : $stealr_group;
                $group_id = find_usergroup_id($stealr_group);
                if ($group_id === null) {
                    break;
                }

                require_code('cns_groups2');
                $members = cns_get_group_members_raw($group_id);

                $victim_count = (count($members) > $victim_count) ? $victim_count : count($members);

                $members_to_steal_ids = array_rand($members, $victim_count);
                if ($victim_count == 1) {
                    $members_to_steal_ids = [$members_to_steal_ids];
                }

                foreach ($members_to_steal_ids as $member_rand_key) {
                    $victim_member_id = $members[$member_rand_key];
                    $victor_member_id = $this->pick_victor($victim_member_id);

                    $total_points = points_balance($victim_member_id);

                    $this->do_point_transfer(min($total_points, $points_to_steal), $victim_member_id, $victor_member_id);
                }

                break;
        }
    }

    protected function pick_victor($victim_member_id)
    {
        $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id<>' . strval($victim_member_id) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
        if (addon_installed('validation')) {
            $sql .= ' AND m_validated=1';
        }
        $sql .= ' ORDER BY ' . db_function('RAND');
        $victor_rows = $GLOBALS['FORUM_DB']->query($sql, 1);
        $victor_member_id = isset($victor_rows[0]['id']) ? $victor_rows[0]['id'] : null;
        return $victor_member_id;
    }

    protected function do_point_transfer($points_to_steal, $victim_member_id, $victor_member_id)
    {
        require_code('points2');

        // Get STOLEN points
        points_debit_member($victim_member_id, do_lang('STEALR_GET', integer_format($points_to_steal, 0)), $points_to_steal, 0, 0, false);

        if ($victor_member_id !== null) {
            // Give STOLEN points
            points_credit_member($victor_member_id, do_lang('STEALR_GAVE_YOU', $points_to_steal, integer_format($points_to_steal, 0)), $points_to_steal, 0, null);

            // Create private topic to message about it...

            require_code('cns_topics_action');
            $victim_displayname = $GLOBALS['FORUM_DRIVER']->get_username($victim_member_id, true);
            $victor_displayname = $GLOBALS['FORUM_DRIVER']->get_username($victor_member_id, true);
            $victim_username = $GLOBALS['FORUM_DRIVER']->get_username($victim_member_id);
            $victor_username = $GLOBALS['FORUM_DRIVER']->get_username($victor_member_id);
            $subject = do_lang('STEALR_PT_TOPIC', integer_format($points_to_steal, 0), $victim_displayname, [$victor_displayname, $victim_username, $victor_username]);
            $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, $victim_member_id, $victor_member_id, false, 0, null, '');

            require_code('cns_posts_action');
            $post_id = cns_make_post($topic_id, $subject, do_lang('STEALR_PT_TOPIC_POST'), 0, true, 1, 0, null, null, null, $victor_member_id, null, null, null, false, true, null, true, $subject, null, true, true, true);

            require_code('cns_topics_action2');
            send_pt_notification($post_id, $subject, $topic_id, $victor_member_id, $victim_member_id);
            send_pt_notification($post_id, $subject, $topic_id, $victim_member_id, $victor_member_id);
        }
    }
}
