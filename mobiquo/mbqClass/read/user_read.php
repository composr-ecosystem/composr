<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
    public function get_user_details(int $user_id) : array
    {
        cms_verify_parameters_phpdoc();

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id, false, USERNAME_DEFAULT_ERROR);

        $user_type = 'normal';
        if ($GLOBALS['FORUM_DRIVER']->is_banned($user_id)) {
            $user_type = 'banned';
        } elseif ((addon_installed('validation')) && ($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_validated') == 0)) {
            $user_type = 'unapproved';
        } elseif ($GLOBALS['FORUM_DRIVER']->is_super_admin($user_id)) {
            $user_type = 'admin';
        } elseif ($GLOBALS['FORUM_DRIVER']->is_staff($user_id)) {
            $user_type = 'mod';
        }

        $ignored_uids = implode(',', array_map('strval', $this->get_ignored_user_ids($user_id)));

        $arr = [
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
        ];

        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user_id, true);
        if ($display_text != $username) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
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
    public function get_posting_setting(int $user_id, string $type)
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
                require_code('images');
                return get_allowed_image_file_types(IMAGE_CRITERIA_WEBSAFE);
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

        return null;
    }

    /**
     * Find which members are ignored.
     *
     * @param  MEMBER $user_id Member ID
     * @return array List of member IDs
     */
    public function get_ignored_user_ids(int $user_id) : array
    {
        cms_verify_parameters_phpdoc();

        $user_ids = [];

        if (!is_guest()) {
            /* This really isn't as designed. Chat blocking != Forum blocking
            $chat_blocked = $GLOBALS['SITE_DB']->query_select('chat_blocking', ['member_blocked'], ['member_blocker' => $user_id]);
            $user_ids = array_merge(collapse_1d_complexity('member_blocked', $chat_blocked), $user_ids);
            */
        }

        /* We don't really want to filter these, would confuse
        $banned = $GLOBALS['SITE_DB']->query_select('usersubmitban_member', ['the_member']);
        $user_ids = array_merge(collapse_1d_complexity('the_member', $banned), $user_ids);
        */

        return array_unique($user_ids);
    }

    /**
     * Get basic stats for inbox.
     *
     * @return array Basic stats
     */
    public function get_inbox_stats() : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        return [
            'inbox_unread_count' => get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_INBOX),
            'subscribed_topic_unread_count' => get_num_unread_topics(null, true),
        ];
    }

    /**
     * Get list of online members.
     *
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @param  ?string $id Resource ID (null: site-wide)
     * @param  ?string $area Resource type (null: site-wide)
     * @set forum topic
     * @return array List of online members
     */
    public function get_online_users(int $start, int $max, ?string $id, ?string $area) : array
    {
        cms_verify_parameters_phpdoc();

        if ($id === null) {
            $sessions = $GLOBALS['SITE_DB']->query_select('sessions', ['DISTINCT member_id'], ['session_invisible' => 0], ' AND member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()), $max, $start);

            $member_count = $GLOBALS['SITE_DB']->query_select_value('sessions', 'COUNT(DISTINCT member_id)', ['session_invisible' => 0], ' AND member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()));

            $list = [];
            foreach ($sessions as $session) {
                $member_id = $session['member_id'];

                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);

                $list[] = [
                    'user_id' => $member_id,
                    'username' => $username,
                ];
            }

            $guest_count = $GLOBALS['SITE_DB']->query_select_value('sessions', 'COUNT(*)', ['member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id()]);
        } else {
            switch ($area) {
                case 'forum':
                    $cms_page = 'forumview';
                    break;
                case 'topic':
                    $cms_page = 'topicview';
                    break;
                default: // Shouldn't happen according to spec, but spec may be extended
                    return [
                        'member_count' => 0,
                        'guest_count' => 0,
                        'list' => [],
                    ];
            }

            $where = ['mt_page' => $cms_page, 'mt_id' => $id];
            $members_viewing = $GLOBALS['FORUM_DB']->query_select('member_tracking', ['mt_member_id'], $where, ' AND mt_member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()), $max, $start);
            $member_count = $GLOBALS['FORUM_DB']->query_select_value('member_tracking', 'COUNT(*)', $where, ' AND mt_member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()));

            $list = [];
            foreach ($members_viewing as $member_viewing) {
                $member_id = $member_viewing['mt_member_id'];

                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);

                $list[] = [
                    'user_id' => $member_id,
                    'username' => $username,
                ];
            }

            $guest_count = $GLOBALS['FORUM_DB']->query_select_value('member_tracking', 'COUNT(*)', ['mt_page' => $cms_page, 'mt_id' => strval($id), 'mt_member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id()]);
        }

        return [
            'member_count' => $member_count,
            'guest_count' => $guest_count,
            'list' => $list,
        ];
    }

    /**
     * Get details of a member.
     *
     * @param  MEMBER $user_id Member ID
     * @return array Details
     */
    public function get_user_info(int $user_id) : array
    {
        cms_verify_parameters_phpdoc();

        push_query_limiting(false);

        require_code('cns_general');
        $need = [
            'username',
            'posts',
            'join_time',
            'last_visit_time',
            'online',
            'likes',
            'liked',
            'avatar',
            'banned',
            'display_name',
            'current_action',
            'custom_fields',
            'dob_label',
            'dob',
            'points',
            'browser',
            'operating_system',
            'secondary_groups',
            'poster_title',
            'time_for_them',
            'num_warnings',
            'signature_comcode',
            'custom_fields_list',
        ];
        $member_info = cns_read_in_member_profile($user_id, $need);

        $user_info = [
            'user_id' => $user_id,
            'username' => $member_info['username'],
            'post_count' => $member_info['posts'],
            'reg_time' => $member_info['join_time'],
            'last_activity_time' => $member_info['last_visit_time'],
            'is_online' => $member_info['online'],

            'accept_pm' => cns_may_make_private_topic() && cns_may_whisper($user_id),
            'i_follow_u' => $member_info['likes'],
            'u_follow_me' => $member_info['liked'],

            'accept_follow' => true,
            'following_count' => $this->get_member_follow_count($user_id, false),
            'follower' => $this->get_member_follow_count($user_id, true),

            'icon_url' => isset($member_info['avatar']) ? $member_info['avatar'] : '',
            'can_ban' => can_ban_member($user_id),
            'is_ban' => $member_info['banned'],
        ];

        if ($member_info['display_name'] != $member_info['username']) {
            $user_info += [
                'display_text' => mobiquo_val($member_info['display_name'], 'base64'),
            ];
        }

        if (isset($member_info['current_action'])) {
            $user_info['current_action'] = $member_info['current_action'];
        }

        $custom_fields_list = [];

        if (has_privilege(get_member(), 'view_profiles')) {
            $value = null;
            require_code('encryption');
            foreach ($member_info['custom_fields'] as $name => $_value) {
                $value = $_value['RAW'];
                $rendered_value = $_value['RENDERED'];

                $encrypted_value = '';
                if (is_data_encrypted($value)) {
                    continue;
                } elseif (is_integer($value)) {
                    $value = strval($value);
                } elseif (is_float($value)) {
                    $value = float_format($value);
                }

                if (((!is_object($value)) && ($value != '')) || ((is_object($value)) && (!$value->is_empty()))) {
                    $_value = strip_html(is_object($rendered_value) ? $rendered_value->evaluate() : $rendered_value);
                    if ($_value != '') {
                        $custom_fields_list[$name] = $_value;
                    }
                }
            }
            if (isset($member_info['dob'])) {
                $custom_fields_list[$member_info['dob_label']] = $member_info['dob'];
            }

            if (addon_installed('points')) {
                require_code('points');
                $custom_fields_list[do_lang('POINTS')] = integer_format($member_info['points']);
            }

            if ((has_privilege(get_member(), 'show_user_browsing')) && (addon_installed('stats'))) {
                if (isset($member_info['browser'])) {
                    $custom_fields_list[do_lang('USER_AGENT')] = $member_info['browser'];
                }
                if (isset($member_info['operating_system'])) {
                    $custom_fields_list[do_lang('USER_OS')] = $member_info['operating_system'];
                }
            }

            foreach ($member_info['secondary_groups'] as $i => $group_id) {
                $custom_fields_list[do_lang('USERGROUP') . ' #' . strval($i + 1)] = cns_get_group_name($group_id, true);
            }

            if (isset($member_info['poster_title'])) {
                $custom_fields_list[do_lang('TITLE')] = $member_info['poster_title'];
            }

            $custom_fields_list[do_lang('TIME_FOR_THEM')] = $member_info['time_for_them'];

            if ((has_privilege(get_member(), 'see_warnings')) && (addon_installed('cns_warnings'))) {
                $num_warnings = $member_info['num_warnings'];
                if ($num_warnings != 0) {
                    $custom_fields_list[do_lang('NUM_WARNINGS')] = integer_format($num_warnings);
                }
            }
        }

        if (isset($member_info['signature_comcode'])) {
            $signature = strip_comcode($member_info['signature_comcode']);
            if ($signature != '') {
                $custom_fields_list[do_lang('SIGNATURE')] = $signature;
            }
        }

        $user_info['custom_fields_list'] = $custom_fields_list;

        return $user_info;
    }

    /**
     * Find friend count.
     *
     * @param  MEMBER $user_id Member involved
     * @param  boolean $i_follow Whether it is friends that member has (otherwise it is people who have friended that member)
     * @return integer Total
     */
    private function get_member_follow_count(int $user_id, bool $i_follow = true) : int
    {
        if (!addon_installed('chat')) {
            return 0;
        }

        if ($i_follow) {
            return $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', ['member_likes' => $user_id]);
        }
        return $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', ['member_liked' => $user_id]);
    }

    /**
     * Get a member's topics.
     *
     * @param  MEMBER $user_id Member ID
     * @param  integer $max Maximum results
     * @return array Topics
     */
    public function get_user_topics(int $user_id, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id';

        $select = ['*', 't.id AS topic_id', 'f.id AS forum_id', 'p.id AS post_id'];

        $where = ['t_cache_first_member_id' => $user_id];
        if (addon_installed('validation')) {
            $where['t_validated'] = 1;
        }

        $extra = ' AND t.t_forum_id IN (' . get_allowed_forum_sql() . ') ORDER BY t_cache_last_time DESC';

        $_topics = (get_allowed_forum_sql() == '') ? [] : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra, $max);

        $topics = [];
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
    public function get_user_reply_posts(int $user_id, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_posts p' . $GLOBALS['FORUM_DB']->prefer_index('f_posts', 'posts_by_in_forum');
        $table .= ' JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id';

        $select = ['*', 'p.id AS post_id', 'f.id AS forum_id', 't.id AS topic_id'];

        $where = ['p_poster' => $user_id];
        if (addon_installed('validation')) {
            $where['p_validated'] = 1;
        }

        $extra = ' AND p.p_cache_forum_id IN (' . get_allowed_forum_sql() . ') ORDER BY p_time DESC,p.id DESC';

        $_posts = (get_allowed_forum_sql() == '') ? [] : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra, $max);

        $posts = [];
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
    public function get_recommended_users(int $start, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return [0, []];
        }

        if (!addon_installed('chat')) {
            return [0, []];
        }

        $users = $GLOBALS['SITE_DB']->query_select('chat_friends', ['member_liked', 'date_and_time'], ['member_likes' => get_member()], 'ORDER BY date_and_time DESC', $max, $start);
        $total = $GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)', ['member_likes' => get_member()]);
        return [$total, $users];
    }

    /**
     * Do a member search.
     *
     * @param  string $keywords Search keywords
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array A pair: total members, members
     */
    public function get_search_users(string $keywords, int $start, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $sql = 'FROM ' . $table_prefix . 'f_members WHERE m_username LIKE \'' . db_encode_like('%' . $keywords . '%') . '\' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
        if (addon_installed('validation')) {
            $sql .= ' AND m_validated=1';
        }
        $sql .= ' ORDER BY m_username';
        $users = $GLOBALS['FORUM_DB']->query('SELECT id,m_username ' . $sql, $max, $start);
        $total = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) ' . $sql);
        return [$total, $users];
    }

    /**
     * Retrieve some contacts (in our case a member).
     *
     * @param  array $user_ids Member IDs
     * @return array List of contact details
     */
    public function get_contact(array $user_ids) : array
    {
        cms_verify_parameters_phpdoc();

        $api_key = get_option('tapatalk_api_key');

        $tt_cipher = new TT_Cipher();

        $users = [];
        foreach ($user_ids as $user_id) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);

            $users[] = [
                'user_id' => $user_id,
                'display_name' => $username,
                'enc_email' => base64_encode($tt_cipher->encrypt($GLOBALS['FORUM_DRIVER']->get_member_email_address($user_id), $api_key)),
                'allow_email' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_allow_emails') == 1,
                'language' => $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_language'),
                'activated' => ($GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_validated_email_confirm_code') == ''),
            ];
        }
        return $users;
    }
}
