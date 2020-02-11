<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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
     * @param  boolean $calculate_num_queued Calculate the number of items queued, if possible
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info($last_run, $calculate_num_queued)
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
            'label' => 'Stealr',
            'num_queued' => null,
            'minutes_between_runs' => 60 * 7 * 24,
        ];
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run($last_run)
    {
        require_code('cns_topics_action2');
        require_code('points');
        require_lang('stealr');

        $stealr_type = get_option('stealr_type');
        if ($stealr_type == '') {
            $stealr_type = 'Members that are inactive, but has lots points';
        }

        $stealr_number = intval(get_option('stealr_number'));
        $stealr_points = intval(get_option('stealr_points'));
        $stealr_group = get_option('stealr_group');

        // Start determining the various cases
        if ($stealr_type == 'Members that are inactive, but has lots points') {
            $all_members = $GLOBALS['FORUM_DRIVER']->get_top_posters(1000);
            $points = [];
            foreach ($all_members as $member) {
                $id = $GLOBALS['FORUM_DRIVER']->mrow_id($member);
                $signin_time = $member['m_last_visit_time'];
                $points[$signin_time] = ['points' => available_points($id), 'id' => $id];
            }

            ksort($points);

            $stealr_number = (count($points) > $stealr_number) ? $stealr_number : count($points);
            $theft_count = 0;

            foreach ($points as $member) {
                $theft_count++;

                if ($theft_count > $stealr_number) {
                    break;
                }

                // Start stealing
                require_code('points2');
                require_lang('stealr');

                $total_points = $member['points'];
                $stealr_points = ($stealr_points < $total_points) ? $stealr_points : $total_points;

                $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id<>' . strval($member['id']) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                if (addon_installed('unvalidated')) {
                    $sql .= ' AND m_validated=1';
                }
                $sql .= ' ORDER BY ' . db_function('RAND');
                $give_to_member = $GLOBALS['FORUM_DB']->query($sql, 1);

                $give_to_member = (isset($give_to_member[0]['id']) && $give_to_member[0]['id'] > 0) ? $give_to_member[0]['id'] : 0;

                // Get THIEF points
                charge_member($member['id'], $stealr_points, do_lang('STEALR_GET') . ' ' . strval($stealr_points) . ' point(-s) from you.');

                if ($give_to_member > 0) {
                    system_gift_transfer(do_lang('STEALR_GAVE_YOU') . ' ' . strval($stealr_points) . ' point(-s)', $stealr_points, $give_to_member);

                    $thief_displayname = $GLOBALS['FORUM_DRIVER']->get_username($member['id'], true);
                    $target_displayname = $GLOBALS['FORUM_DRIVER']->get_username($give_to_member, true);
                    $thief_username = $GLOBALS['FORUM_DRIVER']->get_username($member['id']);
                    $target_username = $GLOBALS['FORUM_DRIVER']->get_username($give_to_member);
                    $subject = do_lang('STEALR_PT_TOPIC', strval($stealr_points), $thief_displayname, [$target_displayname, $thief_username, $target_username]);
                    $body = do_lang('STEALR_PT_TOPIC_POST', strval($stealr_points), $thief_displayname, [$target_displayname, $thief_username, $target_username]);

                    require_code('cns_topics_action');
                    require_code('cns_posts_action');

                    $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, $member['id'], $give_to_member, false, 0, null, '');

                    $post_id = cns_make_post($topic_id, $subject, $body, 0, true, 1, 0, null, null, null, $give_to_member, null, null, null, false, true, null, true, $subject, null, true, true, true);

                    send_pt_notification($post_id, $subject, $topic_id, $give_to_member, $GLOBALS['FORUM_DRIVER']->mrow_id($member));
                    send_pt_notification($post_id, $subject, $topic_id, $GLOBALS['FORUM_DRIVER']->mrow_id($member), $give_to_member);
                }
            }
        } elseif ($stealr_type == 'Members that are rich') {
            $all_members = $GLOBALS['FORUM_DRIVER']->get_top_posters(100);
            $points = [];
            foreach ($all_members as $member) {
                $id = $GLOBALS['FORUM_DRIVER']->mrow_id($member);
                $points[$id] = available_points($id);
            }
            arsort($points);

            $stealr_number = (count($points) > $stealr_number) ? $stealr_number : count($points);
            $theft_count = 0;

            foreach ($points as $member_id => $av_points) {
                $theft_count++;

                if ($theft_count > $stealr_number) {
                    break;
                }

                // Start stealing
                require_code('points2');
                require_lang('stealr');

                $total_points = $av_points;
                $stealr_points = ($stealr_points < $total_points) ? $stealr_points : $total_points;

                $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id<>' . strval($member_id) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                if (addon_installed('unvalidated')) {
                    $sql .= ' AND m_validated=1';
                }
                $sql .= ' ORDER BY ' . db_function('RAND');
                $give_to_member = $GLOBALS['FORUM_DB']->query($sql, 1);

                $give_to_member = (isset($give_to_member[0]['id']) && $give_to_member[0]['id'] > 0) ? $give_to_member[0]['id'] : 0;

                // Get THIEF points
                charge_member($member_id, $stealr_points, do_lang('STEALR_GET') . ' ' . strval($stealr_points) . ' point(-s) from you.');

                if ($give_to_member > 0) {
                    system_gift_transfer(do_lang('STEALR_GAVE_YOU') . ' ' . strval($stealr_points) . ' point(-s)', $stealr_points, $give_to_member);

                    require_code('cns_topics_action');
                    require_code('cns_posts_action');

                    $subject = do_lang('STEALR_PT_TOPIC', strval($stealr_points));
                    $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, $member_id, $give_to_member, false, 0, null, '');

                    $post_id = cns_make_post($topic_id, $subject, do_lang('STEALR_PT_TOPIC_POST'), 0, true, 1, 0, null, null, null, $give_to_member, null, null, null, false, true, null, true, $subject, null, true, true, true);

                    send_pt_notification($post_id, $subject, $topic_id, $give_to_member, $member_id);
                    send_pt_notification($post_id, $subject, $topic_id, $member_id, $give_to_member);
                }
            }
        } elseif ($stealr_type == 'Members that are random') {
            $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
            if (addon_installed('unvalidated')) {
                $sql .= ' AND m_validated=1';
            }
            $sql .= ' ORDER BY ' . db_function('RAND');
            $random_members = $GLOBALS['FORUM_DB']->query($sql, $stealr_number);

            $stealr_number = (count($random_members) > $stealr_number) ? $stealr_number : count($random_members);

            foreach ($random_members as $member) {
                // Start stealing
                require_code('points2');
                require_lang('stealr');

                $total_points = available_points($member['id']);
                $stealr_points = ($stealr_points < $total_points) ? $stealr_points : $total_points;

                $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id<>' . strval($member['id']) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                if (addon_installed('unvalidated')) {
                    $sql .= ' AND m_validated=1';
                }
                $sql .= ' ORDER BY ' . db_function('RAND');
                $give_to_member = $GLOBALS['FORUM_DB']->query($sql, 1);

                $give_to_member = (isset($give_to_member[0]['id']) && $give_to_member[0]['id'] > 0) ? $give_to_member[0]['id'] : 0;

                // Get THIEF points
                charge_member($member['id'], $stealr_points, do_lang('STEALR_GET') . ' ' . strval($stealr_points) . ' point(-s) from you.');

                if ($give_to_member != 0) {
                    system_gift_transfer(do_lang('STEALR_GAVE_YOU') . ' ' . strval($stealr_points) . ' point(-s)', $stealr_points, $give_to_member);

                    require_code('cns_topics_action');
                    require_code('cns_posts_action');

                    $subject = do_lang('STEALR_PT_TOPIC', strval($stealr_points));
                    $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, $member['id'], $give_to_member, false, 0, null, '');

                    $post_id = cns_make_post($topic_id, $subject, do_lang('STEALR_PT_TOPIC_POST'), 0, true, 1, 0, null, null, null, $give_to_member, null, null, null, false, true, null, true, $subject, null, true, true, true);

                    send_pt_notification($post_id, $subject, $topic_id, $give_to_member, $member['id']);
                    send_pt_notification($post_id, $subject, $topic_id, $member['id'], $give_to_member);
                }
            }
        } elseif ($stealr_type == 'Members that are in a certain usergroup') {
            $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list();

            $group_id = 0;
            foreach ($groups as $id => $group) {
                if ($stealr_group == $group) {
                    $group_id = $id;
                }
            }

            require_code('cns_groups2');
            $members = cns_get_group_members_raw($group_id);

            $stealr_number = (count($members) > $stealr_number) ? $stealr_number : count($members);

            $members_to_steal_ids = array_rand($members, $stealr_number);

            if ($stealr_number == 1) {
                $members_to_steal_ids = ['0' => $members_to_steal_ids];
            }

            foreach ($members_to_steal_ids as $member_rand_key) {
                // Start stealing
                require_code('points2');
                require_lang('stealr');

                $total_points = available_points($members[$member_rand_key]);
                $stealr_points = ($stealr_points < $total_points) ? $stealr_points : $total_points;

                $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id<>' . strval($members[$member_rand_key]) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                if (addon_installed('unvalidated')) {
                    $sql .= ' AND m_validated=1';
                }
                $sql .= ' ORDER BY ' . db_function('RAND');
                $give_to_member = $GLOBALS['FORUM_DB']->query($sql, 1);

                $give_to_member = (isset($give_to_member[0]['id']) && $give_to_member[0]['id'] > 0) ? $give_to_member[0]['id'] : 0;

                // Get THIEF points
                charge_member($members[$member_rand_key], $stealr_points, do_lang('STEALR_GET') . ' ' . strval($stealr_points) . ' point(-s) from you.');

                if ($give_to_member != 0) {
                    system_gift_transfer(do_lang('STEALR_GAVE_YOU') . ' ' . strval($stealr_points) . ' point(-s)', $stealr_points, $give_to_member);

                    require_code('cns_topics_action');
                    $subject = do_lang('STEALR_PT_TOPIC', strval($stealr_points));
                    $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, $members[$member_rand_key], $give_to_member, false, 0, null, '');

                    require_code('cns_posts_action');
                    $post_id = cns_make_post($topic_id, $subject, do_lang('STEALR_PT_TOPIC_POST'), 0, true, 1, 0, null, null, null, $give_to_member, null, null, null, false, true, null, true, $subject, null, true, true, true);

                    require_code('cns_topics_action2');
                    send_pt_notification($post_id, $subject, $topic_id, $give_to_member, $stealr_number);
                    send_pt_notification($post_id, $subject, $topic_id, $stealr_number, $give_to_member);
                }
            }
        }
    }
}
