<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: TT_Cipher*/

/**
 * Composr API helper class.
 */
class CMSUserRead
{
    /**
     * Get details of a member who is logged in.
     *
     * @param  MEMBER $user_id Member ID
     * @return array Details
     */
    public function get_user_details($user_id)
    {
        cms_verify_parameters_phpdoc();

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
        if (is_null($username)) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'));
        }

        $user_type = 'normal';
        if ($GLOBALS['FORUM_DRIVER']->is_banned($user_id)) {
            $user_type = 'banned';
        } elseif ((addon_installed('unvalidated')) && ($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_validated') == 0)) {
            $user_type = 'unapproved';
        } elseif ($GLOBALS['FORUM_DRIVER']->is_super_admin($user_id)) {
            $user_type = 'admin';
        } elseif ($GLOBALS['FORUM_DRIVER']->is_staff($user_id)) {
            $user_type = 'mod';
        }

        $ignored_uids = implode(',', array_map('strval', $this->get_ignored_user_ids($user_id)));

        $arr = array(
            'user_id' => $user_id,
            'login_name' => $username,
            'username' => $username,
            'usergroup_id' => $GLOBALS['FORUM_DRIVER']->get_members_groups($user_id),
            'email' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_email_address'),
            'icon_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($user_id),
            'post_count' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_cache_num_posts'),

            'user_type' => $user_type,

            'can_pm' => $this->get_posting_setting($user_id, 'can_pm'),
            'can_send_pm' => $this->get_posting_setting($user_id, 'can_send_pm'),
            'can_moderate' => $this->get_posting_setting($user_id, 'can_moderate'),
            'can_search' => $this->get_posting_setting($user_id, 'can_search'),
            'can_profile' => $this->get_posting_setting($user_id, 'can_profile'),
            'can_whosonline' => has_actual_page_access($user_id, 'users_online'),
            'can_upload_avatar' => $this->get_posting_setting($user_id, 'can_upload_avatar'),

            'max_avatar_width' => $this->get_posting_setting($user_id, 'max_avatar_width'),
            'max_avatar_height' => $this->get_posting_setting($user_id, 'max_avatar_height'),
            'max_attachment' => $this->get_posting_setting($user_id, 'max_attachment'),
            'allowed_extensions' => $this->get_posting_setting($user_id, 'allowed_extensions'),
            'max_attachment_size' => $this->get_posting_setting($user_id, 'max_attachment_size'),
            'max_png_size' => $this->get_posting_setting($user_id, 'max_png_size'),
            'max_jpg_size' => $this->get_posting_setting($user_id, 'max_jpg_size'),

            'post_countdown' => $this->get_posting_setting($user_id, 'post_countdown'), // time required between posts

            'ignored_uids' => $ignored_uids,
        );

        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user_id, true);
        if ($display_text != $username) {
            $arr += array(
                'display_text' => mobiquo_val($display_text, 'base64'),
            );
        }

        return $arr;
    }

    /**
     * Read in a setting for a member's posting abilities.
     *
     * @param  MEMBER $user_id Member ID
     * @param  string $type Setting key
     * @set can_pm can_send_pm can_moderate can_search can_profile can_upload_avatar max_avatar_width max_avatar_height max_attachment allowed_extensions max_attachment_size max_png_size max_jpg_size post_countdown
     * @return mixed Setting value
     */
    public function get_posting_setting($user_id, $type)
    {
        switch ($type) {
            case 'can_pm':
                return true;
            case 'can_send_pm':
                return true;
            case 'can_moderate':
                return cns_may_moderate_forum(db_get_first_id(), $user_id);
            case 'can_search':
                return has_actual_page_access($user_id, 'search');
            case 'can_profile':
                return has_privilege($user_id, 'view_profiles');
            case 'can_upload_avatar':
                return addon_installed('cns_member_avatars');
            case 'max_avatar_width':
                return cns_get_member_best_group_property($user_id, 'max_avatar_width');
            case 'max_avatar_height':
                return cns_get_member_best_group_property($user_id, 'max_avatar_height');
            case 'max_attachment':
                return cns_get_member_best_group_property($user_id, 'max_attachments_per_post');
            case 'allowed_extensions':
                return str_replace(' ', '', get_option('valid_images'));
            case 'max_attachment_size':
            case 'max_png_size':
            case 'max_jpg_size':
                require_code('images');
                return get_max_image_size();
            case 'post_countdown':
                return cns_get_member_best_group_property($user_id, 'flood_control_submit_secs');
            default:
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
    }

    /**
     * Find which members are ignored.
     *
     * @param  MEMBER $user_id Member ID
     * @return array List of member IDs
     */
    public function get_ignored_user_ids($user_id)
    {
        cms_verify_parameters_phpdoc();

        $user_ids = array();

        if (!is_guest()) {
            /* This really isn't as designed. Chat blocking != Forum blocking
            $chat_blocked = $GLOBALS['SITE_DB']->query_select('chat_blocking', array('member_blocked'), array('member_blocker' => $user_id));
            $user_ids = array_merge(collapse_1d_complexity('member_blocked', $chat_blocked), $user_ids);
            */
        }

        /* We don't really want to filter these, would confuse
        $banned = $GLOBALS['SITE_DB']->query_select('usersubmitban_member', array('the_member'));
        $user_ids = array_merge(collapse_1d_complexity('the_member', $banned), $user_ids);
        */

        return array_unique($user_ids);
    }

    /**
     * Get basic stats for inbox.
     *
     * @return array Basic stats
     */
    public function get_inbox_stats()
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        return array(
            'inbox_unread_count' => get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_INBOX),
            'subscribed_topic_unread_count' => get_num_unread_topics(null, true),
        );
    }

    /**
     * Get list of online members.
     *
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @param  ?string $id Resource ID (null: site-wide)
     * @param  ?string $area Resource type (null: site-wide)
     * @set forum topic
     * @return array List of online members.
     */
    public function get_online_users($start, $max, $id, $area)
    {
        cms_verify_parameters_phpdoc();

        if (is_null($id)) {
            $sessions = $GLOBALS['SITE_DB']->query_select('sessions', array('DISTINCT member_id'), array('session_invisible' => 0), ' AND member_id>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()), $max, $start);

            $member_count = $GLOBALS['SITE_DB']->query_select_value('sessions', 'COUNT(DISTINCT member_id)', array('session_invisible' => 0), ' AND member_id>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()));

            $list = array();
            foreach ($sessions as $session) {
                $member_id = $session['member_id'];

                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }

                $list[] = array(
                    'user_id' => $member_id,
                    'username' => $username,
                );
            }

            $guest_count = $GLOBALS['SITE_DB']->query_select_value('sessions', 'COUNT(*)', array('member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id()));
        } else {
            switch ($area) {
                case 'forum':
                    $cms_page = 'forumview';
                    break;
                case 'topic':
                    $cms_page = 'topicview';
                    break;
                default: // Shouldn't happen according to spec, but spec may be extended
                    return array(
                        'member_count' => 0,
                        'guest_count' => 0,
                        'list' => array(),
                    );
            }

            $where = array('mt_page' => $cms_page, 'mt_id' => $id);
            $members_viewing = $GLOBALS['FORUM_DB']->query_select('member_tracking', array('mt_member_id'), $where, ' AND mt_member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()), $max, $start);
            $member_count = $GLOBALS['FORUM_DB']->query_select_value('member_tracking', 'COUNT(*)', $where, ' AND mt_member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()));

            $list = array();
            foreach ($members_viewing as $member_viewing) {
                $member_id = $member_viewing['mt_member_id'];

                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }

                $list[] = array(
                    'user_id' => $member_id,
                    'username' => $username,
                );
            }

            $guest_count = $GLOBALS['FORUM_DB']->query_select_value('member_tracking', 'COUNT(*)', array('mt_page' => $cms_page, 'mt_id' => strval($id), 'mt_member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id()));
        }

        return array(
            'member_count' => $member_count,
            'guest_count' => $guest_count,
            'list' => $list,
        );
    }

    /**
     * Get details of a member.
     *
     * @param  MEMBER $user_id Member ID
     * @return array Details
     */
    public function get_user_info($user_id)
    {
        cms_verify_parameters_phpdoc();

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
        if (is_null($username)) {
            $username = do_lang('UNKNOWN');
        }

        require_code('cns_members2');

        $user_info = array(
            'user_id' => $user_id,
            'username' => $username,
            'post_count' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_cache_num_posts'),
            'reg_time' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_join_time'),
            'last_activity_time' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_last_visit_time'),
            'is_online' => is_member_online($user_id),

            'accept_pm' => cns_may_make_private_topic() && cns_may_whisper($user_id),
            'i_follow_u' => i_follow_u($user_id),
            'u_follow_me' => u_follow_me($user_id),

            'accept_follow' => true,
            'following_count' => $this->get_member_follow_count($user_id, false),
            'follower' => $this->get_member_follow_count($user_id, true),

            'icon_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($user_id),
            'can_ban' => can_ban_member($user_id),
            'is_ban' => $GLOBALS['FORUM_DRIVER']->is_banned($user_id),
        );

        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user_id, true);
        if ($display_text != $username) {
            $user_info += array(
                'display_text' => mobiquo_val($display_text, 'base64'),
            );
        }

        $at_title = $GLOBALS['SITE_DB']->query_select_value_if_there('sessions', 'the_title', array('member_id' => $user_id), 'ORDER BY last_activity DESC');
        if (!is_null($at_title)) {
            $user_info['current_action'] = $at_title;
        }

        require_code('encryption');

        $custom_fields_list = array();

        if (has_privilege(get_member(), 'view_profiles')) {
            $_custom_fields = cns_get_all_custom_fields_match_member(
                $user_id, // member
                ((get_member() != $user_id) && (!has_privilege(get_member(), 'view_any_profile_field'))) ? 1 : null, // public view
                ((get_member() == $user_id) && (!has_privilege(get_member(), 'view_any_profile_field'))) ? 1 : null // owner view
            );
            $value = mixed();
            foreach ($_custom_fields as $name => $_value) {
                $value = $_value['RAW'];
                $rendered_value = $_value['RENDERED'];

                $encrypted_value = '';
                if (is_data_encrypted($value)) {
                    continue;
                } elseif (is_integer($value)) {
                    $value = strval($value);
                } elseif (is_float($value)) {
                    $value = float_to_raw_string($value);
                }

                if (((!is_object($value)) && ($value != '')) || ((is_object($value)) && (!$value->is_empty()))) {
                    $_value = strip_html(is_object($rendered_value) ? $rendered_value->evaluate() : $rendered_value);
                    if ($_value != '') {
                        $custom_fields_list[$name] = $_value;
                    }
                }
            }

            $day = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_dob_day');
            $month = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_dob_month');
            $year = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_dob_year');
            if (($day !== null) && ($month !== null) && ($year !== null)) {
                if ($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_reveal_age') == 1) {
                    if (@strftime('%Y', @mktime(0, 0, 0, 1, 1, 1963)) != '1963') {
                        $dob = strval($year) . '-' . str_pad(strval($month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($day), 2, '0', STR_PAD_LEFT);
                    } else {
                        $dob = get_timezoned_date(mktime(12, 0, 0, $month, $day, $year), false, false, true);
                    }
                    $custom_fields_list[do_lang('DATE_OF_BIRTH')] = $dob;
                } else {
                    $dob = cms_strftime(do_lang('date_no_year'), mktime(12, 0, 0, $month, $day));
                    $custom_fields_list[do_lang('ENTER_YOUR_BIRTHDAY')] = $dob;
                }
            }

            if (addon_installed('points')) {
                require_code('points');
                $custom_fields_list[do_lang('POINTS')] = integer_format(total_points($user_id));
            }

            if ((has_privilege(get_member(), 'show_user_browsing')) && (addon_installed('stats'))) {
                $last_stats = $GLOBALS['SITE_DB']->query_select('stats', array('browser', 'operating_system'), array('member_id' => $user_id), 'ORDER BY date_and_time DESC', 1);
                if (array_key_exists(0, $last_stats)) {
                    $custom_fields_list[do_lang('USER_AGENT')] = $last_stats[0]['browser'];
                    $custom_fields_list[do_lang('USER_OS')] = $last_stats[0]['operating_system'];
                }
            }

            $groups = $GLOBALS['FORUM_DRIVER']->get_members_groups($user_id);
            foreach ($groups as $group_id) {
                $custom_fields_list[do_lang('USERGROUP')] = cns_get_group_name($group_id, true);
            }

            $member_title = get_member_title($user_id);
            if ($member_title != '') {
                $custom_fields_list[do_lang('TITLE')] = $member_title;
            }

            $time_for_them_raw = tz_time(time(), get_users_timezone($user_id));
            $time_for_them = get_timezoned_time(time(), true, $user_id);
            $custom_fields_list[do_lang('TIME_FOR_THEM')] = $time_for_them;

            if ((has_privilege(get_member(), 'see_warnings')) && (addon_installed('cns_warnings'))) {
                $num_warnings = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_cache_warnings');
                if ($num_warnings != 0) {
                    $custom_fields_list[do_lang('NUM_WARNINGS')] = integer_format($num_warnings);
                }
            }
        }

        $signature = strip_comcode(get_translated_text($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_signature'), $GLOBALS['FORUM_DB']));
        if ($signature != '') {
            $custom_fields_list[do_lang('SIGNATURE')] = $signature;
        }

        $user_info['custom_fields_list'] = $custom_fields_list;

        return $user_info;
    }

    /**
     * Find friend count.
     *
     * @param  MEMBER $user_id Member involved
     * @param  boolean $i_follow Whether it is friends that member has (otherwise it is people who have friended that member).
     * @return integer Total
     */
    private function get_member_follow_count($user_id, $i_follow = true)
    {
        if (!addon_installed('chat')) {
            return 0;
        }

        if ($i_follow) {
            return $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', array('member_likes' => $user_id));
        }
        return $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', array('member_liked' => $user_id));
    }

    /**
     * Get a member's topics.
     *
     * @param  MEMBER $user_id Member ID
     * @param  integer $max Maximum results
     * @return array Topics
     */
    public function get_user_topics($user_id, $max)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id';

        $select = array('*', 't.id AS topic_id', 'f.id AS forum_id', 'p.id AS post_id');

        $where = array('t_cache_first_member_id' => $user_id);
        if (addon_installed('unvalidated')) {
            $where['t_validated'] = 1;
        }

        $extra = ' AND t.t_forum_id IN (' . get_allowed_forum_sql() . ') ORDER BY t_cache_last_time DESC';

        $_topics = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra, $max);

        $topics = array();
        foreach ($_topics as $topic) {
            $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic);
        }
        return $topics;
    }

    /**
     * Get a member's posts.
     *
     * @param  MEMBER $user_id Member ID
     * @param  integer $max Maximum results
     * @return array Posts
     */
    public function get_user_reply_posts($user_id, $max)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_posts p';
        if (strpos(get_db_type(), 'mysql') !== false) {
            $table .= ' FORCE INDEX (posts_by_in_forum)';
        }
        $table .= ' JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id';

        $select = array('*', 'p.id AS post_id', 'f.id AS forum_id', 't.id AS topic_id');

        $where = array('p_poster' => $user_id);
        if (addon_installed('unvalidated')) {
            $where['p_validated'] = 1;
        }

        $extra = ' AND p.p_cache_forum_id IN (' . get_allowed_forum_sql() . ') ORDER BY p_time DESC,p.id DESC';

        $_posts = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra, $max);

        $posts = array();
        foreach ($_posts as $post) {
            $posts[] = render_post_to_tapatalk($post['post_id'], false, $post, RENDER_POST_FORUM_DETAILS | RENDER_POST_TOPIC_DETAILS);
        }
        return $posts;
    }

    /**
     * Get recommended members to contact (in our case, friends).
     *
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array A pair: total, members
     */
    public function get_recommended_users($start, $max)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return array(0, array());
        }

        if (!addon_installed('chat')) {
            return array(0, array());
        }

        $users = $GLOBALS['SITE_DB']->query_select('chat_friends', array('member_liked'), array('member_likes' => get_member()), 'ORDER BY date_and_time DESC', $max, $start);
        $total = $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', array('member_likes' => get_member()));
        return array($total, $users);
    }

    /**
     * Do a member search.
     *
     * @param  string $keywords Search keywords
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array A pair: total members, members
     */
    public function get_search_users($keywords, $start, $max)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $sql = 'FROM ' . $table_prefix . 'f_members WHERE m_username LIKE \'' . db_encode_like('%' . $keywords . '%') . '\' AND m_validated_email_confirm_code=\'\'';
        if (addon_installed('unvalidated')) {
            $sql .= ' AND m_validated=1';
        }
        $sql .= ' ORDER BY m_username';
        $users = $GLOBALS['FORUM_DB']->query('SELECT id,m_username ' . $sql, $max, $start);
        $total = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) ' . $sql);
        return array($total, $users);
    }

    /**
     * Retrieve some contacts (in our case a member).
     *
     * @param  array $user_ids Member IDs
     * @return array List of contact details
     */
    public function get_contact($user_ids)
    {
        cms_verify_parameters_phpdoc();

        $api_key = get_option('tapatalk_api_key');

        $tt_cipher = new TT_Cipher();

        $users = array();
        foreach ($user_ids as $user_id) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
            if (is_null($username)) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
            }

            $users[] = array(
                'user_id' => $user_id,
                'display_name' => $username,
                'enc_email' => base64_encode($tt_cipher->encrypt($GLOBALS['FORUM_DRIVER']->get_member_email_address($user_id), $api_key)),
                'allow_email' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_allow_emails') == 1,
                'language' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_language'),
                'activated' => ($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_validated_email_confirm_code') == ''),
            );
        }
        return $users;
    }
}
