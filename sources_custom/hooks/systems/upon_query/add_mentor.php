<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

/**
 * Hook class.
 */
class Hook_upon_query_add_mentor
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if ($query[0] == 'S') {
            return;
        }

        if (!isset($GLOBALS['FORUM_DB'])) {
            return;
        }
        if ($GLOBALS['IN_MINIKERNEL_VERSION']) {
            return;
        }

        if (!$GLOBALS['SITE_DB']->table_exists('members_mentors')) {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        //if (strpos($query, $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members') !== false && strpos($query, 'BY RAND') == false) // to test without registration
        if (strpos($query, 'INTO ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members') !== false) {
            load_user_stuff();
            if (method_exists($GLOBALS['FORUM_DRIVER'], 'forum_layer_initialise')) {
                $GLOBALS['FORUM_DRIVER']->forum_layer_initialise();
            }

            $mentor_usergroup = get_option('mentor_usergroup');

            require_code('cns_topics');
            require_code('cns_forums');
            require_code('cns_topics_action');
            require_code('cns_posts_action');
            require_code('cns_topics_action2');
            require_code('cns_posts_action2');
            require_code('cns_members');
            require_code('cns_members2');

            require_lang('mentorr');

            $mentor_usergroup_id = mixed();
            $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list();
            foreach ($groups as $group_id => $group) {
                if ($group == $mentor_usergroup) {
                    $mentor_usergroup_id = $group_id;
                }
            }
            if ($mentor_usergroup_id === null) {
                return;
            }

            $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_group_members g ON (g.gm_member_id=m.id AND gm_validated=1) WHERE gm_group_id=' . strval($mentor_usergroup_id) . ' OR m_primary_group=' . strval($mentor_usergroup_id) . ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
            if (addon_installed('unvalidated')) {
                $sql .= ' AND m_validated=1';
            }
            $sql .= ' ORDER BY ' . db_function('RAND');
            $mentor_id = $GLOBALS['FORUM_DB']->query_value_if_there($sql, true);
            if ($mentor_id === null) {
                return;
            }
            $member_id = $ret;
            $time = time();

            $GLOBALS['SITE_DB']->query_delete('chat_friends', array(
                'member_likes' => $mentor_id,
                'member_liked' => $member_id
            ), '', 1); // Just in case page refreshed

            $GLOBALS['SITE_DB']->query_insert('chat_friends', array(
                'member_likes' => $mentor_id,
                'member_liked' => $member_id,
                'date_and_time' => $time
            ));

            $GLOBALS['SITE_DB']->query_delete('members_mentors', array(
                'member_id' => $member_id,
                'mentor_id' => $mentor_id,
            ), '', 1); // Just in case page refreshed

            $GLOBALS['SITE_DB']->query_insert('members_mentors', array(
                'member_id' => $member_id,
                'mentor_id' => $mentor_id,
            ));

            log_it('MAKE_FRIEND', strval($mentor_id), strval($member_id));

            $subject = do_lang('MENTOR_PT_TOPIC', $GLOBALS['FORUM_DRIVER']->get_username($mentor_id, true), $GLOBALS['FORUM_DRIVER']->get_username($member_id));
            $topic_id = cns_make_topic(null, '', '', 1, 1, 0, 0, 0, $mentor_id, $member_id, false, 0, null, '');
            $body = do_lang('MENTOR_PT_TOPIC_POST', comcode_escape($GLOBALS['FORUM_DRIVER']->get_username($mentor_id)), comcode_escape($GLOBALS['FORUM_DRIVER']->get_username($member_id)), array(comcode_escape(get_site_name()), comcode_escape($GLOBALS['FORUM_DRIVER']->get_username($mentor_id, true)), comcode_escape($GLOBALS['FORUM_DRIVER']->get_username($member_id))));
            $post_id = cns_make_post($topic_id, $subject, $body, 0, true, 1, 0, null, null, null, $mentor_id, null, null, null, false, true, null, true, $subject, 0, null, true, true, true);
            send_pt_notification($post_id, $subject, $topic_id, $member_id, $mentor_id);
            send_pt_notification($post_id, $subject, $topic_id, $mentor_id, $member_id);
        }
    }
}
