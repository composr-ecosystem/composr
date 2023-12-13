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

/*EXTRA FUNCTIONS: file_put_contents*/

define('TAPATALK_POST_LIVE', 0);
define('TAPATALK_POST_NEEDS_VALIDATION', 1);

define('RENDER_TOPIC_POST_KEY_NAME', 1);
define('RENDER_TOPIC_MODERATED_BY', 2);
define('RENDER_TOPIC_DEEP_PERMISSIONS', 4);
define('RENDER_TOPIC_SEARCH', 8);
define('RENDER_TOPIC_LAST_POSTER', 16);

define('RENDER_POST_SHORT_CONTENT', 1);
define('RENDER_POST_FORUM_DETAILS', 2);
define('RENDER_POST_TOPIC_DETAILS', 4);
define('RENDER_POST_MODERATED_BY', 8);
define('RENDER_POST_RESULT_TRUE', 16);
define('RENDER_POST_SEARCH', 32);

/**
 * Find whether a topic has unread posts.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member to check for (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether there are
 */
function is_topic_unread($topic_id, $member_id = null, $topic_details = null)
{
    return !cns_has_read_topic($topic_id, is_null($topic_details) ? null : $topic_details['t_cache_last_time'], $member_id);
}

/**
 * Get number of unread topics in a forum.
 *
 * @param  ?AUTO_LINK $forum_id Forum ID (null: all forums)
 * @param  boolean $subscribed_only Whether only look at subscribed topics
 * @param  ?MEMBER $member_id Member to check for (null: current member)
 * @param  boolean $only_read_one Whether to give up counting after hitting one
 * @return integer The count
 */
function get_num_unread_topics($forum_id, $subscribed_only = false, $member_id = null, $only_read_one = false)
{
    if (is_null($member_id)) {
        $member_id = get_member();
    }

    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

    $sql = 'FROM ' . $table_prefix . 'f_topics t';
    $sql .= ' LEFT JOIN ' . $table_prefix . 'f_read_logs r ON t.id=r.l_topic_id AND l_member_id=' . strval($member_id);
    $sql .= ' WHERE';
    if (is_null($forum_id)) {
        $sql .= ' t.t_forum_id IS NOT NULL';
    } else {
        $sql .= ' ' . cns_get_all_subordinate_forums($forum_id, 't.t_forum_id');
    }
    $sql .= ' AND (l_time IS NULL OR l_time<t_cache_last_time)'; // Cannot get join match OR gets one and it is behind of last post
    $sql .= ' AND t_cache_last_time>' . strval(time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))); // Within tracking range
    if (addon_installed('unvalidated')) {
        $sql .= ' AND t_validated=1';
    }

    if ($subscribed_only) {
        $sql .= ' AND EXISTS(SELECT l_code_category FROM ' . $table_prefix . 'notifications_enabled WHERE l_member_id=' . strval($member_id) . ' AND ' . db_string_equal_to('l_notification_code', 'cns_topic') . ' AND l_code_category=t.id)';
    }

    if ($only_read_one) {
        $test = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT t.id ' . $sql);
        $ret = is_null($test) ? 0 : 1;
    } else {
        $ret = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) ' . $sql);
    }
    return $ret;
}

/**
 * Find whether there are unread topics/posts in a forum.
 *
 * @param  AUTO_LINK $forum_id Forum ID
 * @param  ?MEMBER $member_id Member to check for (null: current member)
 * @return boolean Whether there are
 */
function is_forum_unread($forum_id, $member_id = null)
{
    return (get_num_unread_topics($forum_id, false, $member_id, true) > 0);
}

/**
 * Whether a resource is validated.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  AUTO_LINK $id Resource ID
 * @param  ?array $details Resource row (null: lookup)
 * @return boolean Whether it is
 */
function is_approved($type, $id, $details = null)
{
    if (!addon_installed('unvalidated')) {
        return true;
    }
    if ($type == 'topic') {
        if (!is_null($details)) {
            $result = $details['t_validated'];
        } else {
            $result = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 't_validated', array('id' => $id));
        }
    } else {
        if (!is_null($details)) {
            $result = $details['p_validated'];
        } else {
            $result = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_validated', array('id' => $id));
        }
    }
    return ($result == 1);
}

/**
 * Whether a topic is pinned.
 *
 * @param  AUTO_LINK $id Topic ID
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether it is
 */
function is_sticky_topic($id, $topic_details = null)
{
    if (!is_null($topic_details)) {
        $pinned = $topic_details['t_pinned'];
    } else {
        $pinned = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 't_pinned', array('id' => $id));
    }
    return ($pinned == 1);
}

/**
 * Whether member has notifications set on a forum.
 *
 * @param  AUTO_LINK $forum_id Forum ID
 * @param  ?MEMBER $member_id Member to check for (null: current member)
 * @return boolean Whether they are are
 */
function get_forum_subscription_status($forum_id, $member_id = null)
{
    if (is_guest($member_id)) {
        return false;
    }

    require_code('notifications');
    return notifications_enabled('cns_topic', 'forum:' . strval($forum_id), $member_id);
}

/**
 * Whether member has notifications set on a topic.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member to check for (null: current member)
 * @return boolean Whether they are are
 */
function get_topic_subscription_status($topic_id, $member_id = null)
{
    if (is_guest($member_id)) {
        return false;
    }

    require_code('notifications');
    return notifications_enabled('cns_topic', strval($topic_id), $member_id);
}

/**
 * Render a topic to Tapatalk's Mobiquo structure.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  boolean $return_html Whether to return HTML for post data
 * @param  ?integer $start Start position for topic post retrieval (null: return no posts)
 * @param  ?integer $max Maximum topic posts retrieved (null: return no posts)
 * @param  ?array $details Topic details (null: lookup)
 * @param  integer $behaviour_modifiers A bitmask of RENDER_TOPIC_* settings
 * @param  ?AUTO_LINK $position Post position to scroll to (only used if $start is not null) (null: N/A)
 * @return object Mobiquo array
 */
function render_topic_to_tapatalk($topic_id, $return_html, $start, $max, $details = null, $behaviour_modifiers = 0, $position = null)
{
    $member_id = get_member();

    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

    if (is_null($details)) {
        $_details = $GLOBALS['FORUM_DB']->query_select(
            'f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id',
            array('*', 'f.id as forum_id', 't.id AS topic_id', 'p.id AS post_id'),
            array('t.id' => $topic_id),
            '',
            1
        );
        if (!isset($_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }
        $details = $_details[0];
    }

    $moderation_details = moderation_assessment_topic($details, $member_id, RENDER_TOPIC_DEEP_PERMISSIONS);

    $forum_id = $details['forum_id'];

    $arr = array(
        'forum_id' => mobiquo_val(strval($forum_id), 'string'),
        'forum_name' => mobiquo_val($details['f_name'], 'base64'),
        'topic_id' => mobiquo_val(strval($topic_id), 'string'),
        'topic_title' => mobiquo_val($details['t_cache_first_title'], 'base64'),
    );

    if (($behaviour_modifiers & RENDER_TOPIC_POST_KEY_NAME) == 0) {
        $arr += array(
            'topic_author_id' => mobiquo_val(strval($details['t_cache_first_member_id']), 'string'),
            'topic_author_name' => mobiquo_val(strval($details['t_cache_first_username']), 'base64'),
        );
    } else {
        if (($behaviour_modifiers & RENDER_TOPIC_LAST_POSTER) == 0) {
            $arr += array(
                'post_author_id' => mobiquo_val(strval($details['t_cache_first_member_id']), 'string'),
                'post_author_name' => mobiquo_val(strval($details['t_cache_first_username']), 'base64'),
                'post_id' => mobiquo_val(strval($details['t_cache_first_post_id']), 'string'),
            );
        } else {
            $arr += array(
                'post_author_id' => mobiquo_val(strval($details['t_cache_last_member_id']), 'string'),
                'post_author_name' => mobiquo_val(strval($details['t_cache_last_username']), 'base64'),
                'post_id' => mobiquo_val(strval($details['t_cache_last_post_id']), 'string'),
            );
        }
    }

    if (($behaviour_modifiers & RENDER_TOPIC_MODERATED_BY) != 0) {
        $last_moderation_details = get_last_moderation_details('topic', strval($topic_id));

        if (!is_null($moderation_details)) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($last_moderation_details['l_by']);
            if (is_null($username)) {
                $username = do_lang('UNKNOWN');
            }

            $arr += array(
                'moderated_by_id' => mobiquo_val(strval($last_moderation_details['l_by']), 'string'),
                'moderated_by_name' => mobiquo_val($username, 'base64'),
                'moderated_reason' => mobiquo_val($last_moderation_details['l_param_b'], 'base64'),
            );
        }
    }

    if (($behaviour_modifiers & RENDER_TOPIC_LAST_POSTER) == 0) {
        $arr += array(
            'post_time' => mobiquo_val($details['t_cache_first_time'], 'dateTime.iso8601'),
            'topic_author_avatar' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($details['t_cache_first_member_id']), 'string'),
            'icon_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($details['t_cache_first_member_id']),
        );
    } else {
        $arr += array(
            'post_time' => mobiquo_val($details['t_cache_last_time'], 'dateTime.iso8601'),
            'topic_author_avatar' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($details['t_cache_last_member_id']), 'string'),
            'icon_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($details['t_cache_last_member_id']),
        );
    }

    $arr += array(
        'timestamp' => mobiquo_val(strval($details['t_cache_last_time']), 'string'),
        'last_reply_time' => mobiquo_val($details['t_cache_last_time'], 'dateTime.iso8601'),
        'last_reply_author_name' => mobiquo_val($details['t_cache_last_username'], 'base64'),
        'last_reply_author_id' => mobiquo_val($details['t_cache_last_member_id'], 'string'),
    );

    $arr += array(
        'view_number' => mobiquo_val($details['t_num_views'], 'int'),
        'prefix' => mobiquo_val('', 'base64'),
        'is_subscribed' => mobiquo_val(get_topic_subscription_status($topic_id), 'boolean'),
        'can_subscribe' => mobiquo_val(!is_guest(), 'boolean'),
        'is_poll' => mobiquo_val($details['t_poll_id'] !== null, 'boolean'),
        'is_closed' => mobiquo_val($details['t_is_open'] == 0, 'boolean'),
        'can_report' => mobiquo_val(true, 'boolean'),
        'can_reply' => mobiquo_val(can_reply_to_topic($topic_id, $member_id, $details), 'boolean'),
        'can_merge' => mobiquo_val($moderation_details['can_merge'], 'boolean'),
        'can_merge_post' => mobiquo_val($moderation_details['can_merge_post'], 'boolean'),
        'can_rename' => mobiquo_val($moderation_details['can_rename'], 'boolean'),
        'is_deleted' => mobiquo_val($moderation_details['is_deleted'], 'boolean'),
        'can_delete' => mobiquo_val($moderation_details['can_delete'], 'boolean'),
        'can_close' => mobiquo_val($moderation_details['can_close'], 'boolean'),
        'is_approved' => mobiquo_val($moderation_details['is_approved'], 'boolean'),
        'can_approve' => mobiquo_val($moderation_details['can_approve'], 'boolean'),
        'is_sticky' => mobiquo_val($moderation_details['is_sticky'], 'boolean'),
        'can_stick' => mobiquo_val($moderation_details['can_stick'], 'boolean'),
        'can_move' => mobiquo_val($moderation_details['can_move'], 'boolean'),
        'is_ban' => mobiquo_val($moderation_details['is_ban'], 'boolean'),
        'can_ban' => mobiquo_val($moderation_details['can_ban'], 'boolean'),
        'can_mark_spam' => mobiquo_val($moderation_details['can_ban'], 'boolean'), // will be overridden later, if we have a jump position set
        'position' => mobiquo_val($start + 1, 'int'),
        'reply_number' => mobiquo_val($details['t_cache_num_posts'] - 1, 'int'),
        'new_post' => mobiquo_val(is_topic_unread($topic_id, $member_id, $details), 'boolean'),
        'short_content' => mobiquo_val(generate_shortened_post($details, true), 'base64'),
        /*'is_moved' =>, We don't have these topic shells in Composr
        'is_merged' =>,
        'real_topic_id' => ,*/
        'can_upload' => mobiquo_val(true, 'boolean'),
    );

    if (($behaviour_modifiers & RENDER_TOPIC_SEARCH) == 0) {
        $arr['breadcrumb'] = mobiquo_val(build_forum_breadcrumbs($forum_id), 'array');
    }

    $arr['can_thank'] = mobiquo_val(addon_installed('points'), 'boolean');

    $headers = apache_request_headers();
    if ((isset($headers['Mobiquoid'])) && (intval($headers['Mobiquoid']) == 11)) {
        $participated_uids = get_topic_participant_uids($topic_id, $max);
        $arr['participated_uids'] = mobiquo_val(array_map('strval', $participated_uids), 'array');
    }

    if (!is_null($start)) {
        $sql = '';
        $sql .= ' FROM ' . $table_prefix . 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id';
        $sql .= ' WHERE ' . tapatalk_get_topic_where($topic_id, $member_id);
        $sql .= ' ORDER BY p_time,p.id';
        $_posts = $GLOBALS['FORUM_DB']->query('SELECT *,p.id AS post_id,t.id AS topic_id,f.id AS forum_id' . $sql, $max, $start);

        $sql = '';
        $sql .= ' FROM ' . $table_prefix . 'f_posts p';
        $sql .= ' WHERE ' . tapatalk_get_topic_where($topic_id, $member_id);
        $total_post_count = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) ' . $sql);

        $posts = array();
        foreach ($_posts as $post) {
            $posts[] = render_post_to_tapatalk($post['post_id'], $return_html, $post);
        }
        $arr['posts'] = mobiquo_val($posts, 'array');
        $arr['total_post_num'] = mobiquo_val($total_post_count, 'int');
        if (!is_null($position)) {
            $arr['position'] = mobiquo_val(min($total_post_count, $position), 'int');
        }
    }

    return mobiquo_val($arr, 'struct');
}

/**
 * Get a breadcrumb chain for a forum.
 *
 * @param  AUTO_LINK $forum_id Forum ID
 * @return array Breadcrumb chain of Mobiquo structures
 */
function build_forum_breadcrumbs($forum_id)
{
    $forum_details = $GLOBALS['FORUM_DB']->query_select('f_forums', array('f_name', 'f_parent_forum'), array('id' => $forum_id), '', 1);

    $breadcrumbs = array();
    if ((!is_null($forum_details[0]['f_parent_forum'])) && ($forum_details[0]['f_parent_forum'] != db_get_first_id())) {
        $breadcrumbs = array_merge($breadcrumbs, build_forum_breadcrumbs($forum_details[0]['f_parent_forum']));
    }
    $breadcrumbs[] = mobiquo_val(array(
        'forum_id' => mobiquo_val(strval($forum_id), 'string'),
        'forum_name' => mobiquo_val($forum_details[0]['f_name'], 'base64'),
        'sub_only' => mobiquo_val(false, 'boolean'),
    ), 'struct');
    return $breadcrumbs;
}

/**
 * Get an SQL 'WHERE' clause for the posts in a topic.
 *
 * @param  AUTO_LINK $topic_id The ID of the topic we are getting details of.
 * @param  ?MEMBER $member_id The member ID (null: current member).
 * @return string The WHERE clause.
 */
function tapatalk_get_topic_where($topic_id, $member_id = null)
{
    if (is_null($member_id)) {
        $member_id = get_member();
    }

    $where = 'p_topic_id=' . strval($topic_id);
    if ((addon_installed('unvalidated')) && (!has_privilege($member_id, 'see_unvalidated'))) {
        $where .= ' AND p_validated=1';
    }
    if (!has_privilege(get_member(), 'view_other_pt')) {
        $where .= ' AND (p_intended_solely_for IS NULL OR p_intended_solely_for=' . strval($member_id) . ' OR p_poster=' . strval($member_id) . ')';
    }
    return $where;
}

/**
 * Find participants in a topic (IDs).
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  integer $max Maximum number to retrieve
 * @param  ?array $topic_details Details of the topic (null: assume not a PT, so only look at post participants)
 * @return array List of member IDs
 */
function get_topic_participant_uids($topic_id, $max, $topic_details = null)
{
    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

    $sql = '';
    $sql .= ' FROM ' . $table_prefix . 'f_posts p';
    $sql .= ' WHERE ' . tapatalk_get_topic_where($topic_id);
    $sql .= ' GROUP BY p_poster ORDER BY COUNT(*) DESC';
    $participants = $GLOBALS['FORUM_DB']->query('SELECT p_poster' . $sql, $max);
    $ret = collapse_1d_complexity('p_poster', $participants);
    if ((!is_null($topic_details)) && (is_null($topic_details['t_forum_id']))) {
        $ret[] = $topic_details['t_pt_from'];
        $ret[] = $topic_details['t_pt_to'];
        $ret = array_merge($ret, collapse_1d_complexity('s_member_id', $GLOBALS['FORUM_DB']->query_select('f_special_pt_access', array('s_member_id'), array('s_topic_id' => $topic_id))));
    }
    return array_unique($ret);
}

/**
 * Find participants in a topic (extended details).
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?integer $max Maximum number to retrieve (null: no limit)
 * @param  ?array $topic_details Details of the topic (null: assume not a PT, so only look at post participants)
 * @return array List of maps of member details
 */
function get_topic_participants($topic_id, $max = null, $topic_details = null)
{
    $participants = array();
    foreach (get_topic_participant_uids($topic_id, $max, $topic_details) as $participant) {
        $username = $GLOBALS['FORUM_DRIVER']->get_username($participant);
        if (!is_null($username)) {
            $participants[] = array(
                'user_id' => $participant,
                'username' => $username,
                'icon_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($participant),
                'is_online' => is_member_online($participant),
            );
        }
    }
    return $participants;
}

/**
 * Render a post to Tapatalk's Mobiquo structure.
 *
 * @param  AUTO_LINK $post_id Post ID
 * @param  boolean $return_html Whether to return HTML for post data
 * @param  ?array $post_row Post details (null: lookup)
 * @param  integer $behaviour_modifiers A bitmask of RENDER_POST_* settings
 * @return object Mobiquo array
 */
function render_post_to_tapatalk($post_id, $return_html, $post_row = null, $behaviour_modifiers = 0)
{
    if ($post_row === null) {
        // Load post row back
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post_id), '', 1);
        if (!isset($post_rows[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $post_row = $post_rows[0];
    }

    $member_id = get_member();

    $moderation_details = moderation_assessment_post($post_row, $member_id, $behaviour_modifiers);

    $post_author_id = $post_row['p_poster'];
    $username = $GLOBALS['FORUM_DRIVER']->get_username($post_author_id);
    if (is_null($username)) {
        $username = do_lang('UNKNOWN');
    }

    $likes_info = array();
    $can_like = ($post_author_id != $member_id);
    $is_liked = false;
    if (get_option('is_on_rating') == '1') {
        $likes = $GLOBALS['SITE_DB']->query_select('rating', array('rating_member'), array('rating' => 10, 'rating_for_type' => 'post', 'rating_for_id' => strval($post_id)), '', 100);
        foreach ($likes as $like) {
            $lusername = $GLOBALS['FORUM_DRIVER']->get_username($like['rating_member']);
            if (is_null($username)) {
                $lusername = do_lang('UNKNOWN');
            }

            $_arr = array(
                'userid' => mobiquo_val($like['rating_member'], 'string'),
                'username' => mobiquo_val($lusername, 'base64'),
            );
            $display_text = $GLOBALS['FORUM_DRIVER']->get_username($like['rating_member'], true);
            if ($display_text != $lusername) {
                $_arr += array(
                    'display_text' => mobiquo_val($display_text, 'base64'),
                );
            }
            $likes_info[] = mobiquo_val($_arr, 'struct');

            if ($like['rating_member'] == $member_id) {
                $is_liked = true;
            }
        }
    }

    $arr = array(
        'post_id' => mobiquo_val(strval($post_id), 'string'),
        'post_title' => mobiquo_val($post_row['p_title'], 'base64'),
        'post_author_id' => mobiquo_val(strval($post_author_id), 'string'),
        'post_author_name' => mobiquo_val($username, 'base64'),
        'is_online' => mobiquo_val(is_member_online($post_author_id), 'boolean'),
        'can_edit' => mobiquo_val($moderation_details['can_edit'], 'boolean'),
        'icon_url' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($post_row['p_poster']), 'string'),
        'post_time' => mobiquo_val($post_row['p_time'], 'dateTime.iso8601'),
        'timestamp' => mobiquo_val(strval($post_row['p_time']), 'string'),
        'allow_smilies' => mobiquo_val(true, 'boolean'),
        'like_count' => mobiquo_val(count($likes_info), 'int'),
        'can_like' => mobiquo_val($can_like, 'boolean'),
        'is_liked' => mobiquo_val($is_liked, 'boolean'),
        'can_delete' => mobiquo_val($moderation_details['can_delete'], 'boolean'),
        'is_deleted' => mobiquo_val($moderation_details['is_deleted'], 'boolean'),
        'can_approve' => mobiquo_val($moderation_details['can_approve'], 'boolean'),
        'is_approved' => mobiquo_val($moderation_details['is_approved'], 'boolean'),
        'can_ban' => mobiquo_val($moderation_details['can_ban'], 'boolean'),
        'is_ban' => mobiquo_val($moderation_details['is_ban'], 'boolean'),
        'can_move' => mobiquo_val($moderation_details['can_move'], 'boolean'),
    );

    $validated = $post_row['p_validated'];
    $arr['state'] = mobiquo_val(($validated == 1) ? TAPATALK_POST_LIVE : TAPATALK_POST_NEEDS_VALIDATION, 'int');

    if (addon_installed('points')) {
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $where = array();
        $where['gift_to'] = $post_author_id;
        if (multi_lang_content()) {
            $table = 'gifts g JOIN ' . $table_prefix . 'translate t ON t.id=g.reason';
            $where['text_original'] = do_lang('TAPATALK_THANK_POST', strval($post_id));
        } else {
            $table = 'gifts';
            $where['reason'] = do_lang('TAPATALK_THANK_POST', strval($post_id));
        }
        $thank_count = $GLOBALS['FORUM_DB']->query_select_value($table, 'COUNT(*)', $where);
        $arr['thank_count'] = mobiquo_val($thank_count, 'int');
    } else {
        $arr['thank_count'] = mobiquo_val(0, 'int');
    }

    if (($behaviour_modifiers & RENDER_POST_SHORT_CONTENT) != 0) {
        $content = generate_shortened_post($post_row, $post_id == $post_row['t_cache_first_post_id']);
    } else {
        $content = prepare_post_for_tapatalk($post_row, $return_html);
    }

    $attachments = get_post_attachments($post_id, null, true, $content);

    if (($behaviour_modifiers & RENDER_POST_SEARCH) == 0) {
        $arr += array(
            'attachments' => mobiquo_val(render_tapatalk_attachments($attachments), 'array'),
            'likes_info' => mobiquo_val($likes_info, 'array'),
        );
    }

    if (($behaviour_modifiers & RENDER_POST_SHORT_CONTENT) != 0) {
        $arr['short_content'] = mobiquo_val($content, 'base64');
    } else {
        $arr['post_content'] = mobiquo_val($content, 'base64');
    }

    if (($behaviour_modifiers & RENDER_POST_FORUM_DETAILS) != 0) {
        $arr += array(
            'forum_id' => mobiquo_val(strval($post_row['p_cache_forum_id']), 'string'),
            'forum_name' => mobiquo_val($post_row['f_name'], 'base64'),
        );
    }

    if (($behaviour_modifiers & RENDER_POST_TOPIC_DETAILS) != 0) {
        $arr += array(
            'topic_id' => mobiquo_val(strval($post_row['topic_id']), 'string'),
            'topic_title' => mobiquo_val($post_row['t_cache_first_title'], 'base64'),
            'reply_number' => mobiquo_val($post_row['t_cache_num_posts'] - 1, 'int'),
            'new_post' => mobiquo_val(is_topic_unread($post_row['topic_id'], $member_id, $post_row), 'boolean'),
            'view_number' => mobiquo_val($post_row['t_num_views'], 'int'),
        );
    }

    if (($behaviour_modifiers & RENDER_POST_MODERATED_BY) != 0) {
        $moderation_details = get_last_moderation_details('post', strval($post_row['post_id']));

        if (!is_null($moderation_details)) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($moderation_details['l_by']);
            if (is_null($username)) {
                $username = do_lang('UNKNOWN');
            }

            $arr += array(
                'moderated_by_id' => mobiquo_val(strval($moderation_details['l_by']), 'string'),
                'moderated_by_name' => mobiquo_val($username, 'base64'),
                'moderated_reason' => mobiquo_val($moderation_details['l_param_b'], 'base64'),
            );
        }
    } else {
        if (!is_null($post_row['p_last_edit_time'])) {
            $editor_name = $GLOBALS['FORUM_DRIVER']->get_username($post_row['p_last_edit_by']);
            if (is_null($editor_name)) {
                $editor_name = do_lang('UNKNOWN');
            }

            $edit_reason = '';
            if (has_actual_page_access($member_id, 'admin_actionlog')) {
                $_edit_reason = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_moderator_logs', 'l_reason', array('l_the_type' => 'EDIT_POST', 'l_param_a' => strval($post_id)));
                if (!is_null($_edit_reason)) {
                    $edit_reason = $_edit_reason;
                }
            }

            $arr += array(
                'editor_id' => mobiquo_val(strval($post_row['p_last_edit_by']), 'string'),
                'editor_name' => mobiquo_val($editor_name, 'base64'),
                'edit_time' => mobiquo_val($post_row['p_last_edit_time'], 'int'),
                'edit_reason' => mobiquo_val($edit_reason, 'base64'),
            );
        }
    }

    if (($behaviour_modifiers & RENDER_POST_RESULT_TRUE) != 0) {
        $arr['result'] = mobiquo_val(true, 'boolean');
    }

    return mobiquo_val($arr, 'struct');
}

/**
 * Prepare a post for rendering to Tapatalk.
 *
 * @param  array $post Post row
 * @param  boolean $return_html Return HTML
 * @return string Rendered post
 */
function prepare_post_for_tapatalk($post, $return_html = false)
{
    $content = '';

    if (!is_null($post['p_parent_id'])) {
        $post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics t ON t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post['p_parent_id']), '', 1);
        if (isset($post_details[0])) {
            $poster = $post_details[0]['p_poster_name_if_guest'];
            if ($poster == '') {
                $poster = $GLOBALS['FORUM_DRIVER']->get_username($post_details[0]['p_poster']);
                if (is_null($poster)) {
                    $poster = do_lang('UNKNOWN');
                }
            }

            $content .= '[quote="' . addslashes($poster) . '"]' . get_translated_text($post_details[0]['p_post'], $GLOBALS['FORUM_DB']) . "[/quote]\n\n";
        }
    }

    $content .= get_translated_text($post['p_post'], $GLOBALS['FORUM_DB']);

    $has_poll = false;
    if ((!is_null($post['t_poll_id'])) && ($post['post_id'] == $post['t_cache_first_post_id'])) {
        $has_poll = true;
    }

    $whisper_username = mixed();
    if (!is_null($post['p_intended_solely_for'])) {
        $whisper_username = $GLOBALS['FORUM_DRIVER']->get_username($post['p_intended_solely_for']);
        if (is_null($whisper_username)) {
            $whisper_username = do_lang('UNKNOWN');
        }
    }

    $content = static_evaluate_tempcode(do_template('TAPATALK_POST_WRAPPER', array(
        '_GUID' => 'ef6a156778d1bcaf9228c8bddef938fc',
        'CONTENT' => $content,
        'WHISPER_USERNAME' => $whisper_username,
        'HAS_POLL' => $has_poll,
        'POST_ID' => strval($post['id']),
        'POST_TITLE' => $post['p_title'],
        'POST_URL' => find_script('pagelink_redirect') . '?id=' . get_page_zone('topicview') . ':topicview:findpost:' . strval($post['post_id']), // Redirect needed so not detected as a local URL
        'POSTER_ID' => strval($post['p_poster']),
        'LAST_EDIT_TIME' => is_null($post['p_last_edit_time']) ? null : strval($post['p_last_edit_time']),
        'LAST_EDIT_BY' => is_null($post['p_last_edit_by']) ? null : strval($post['p_last_edit_by']),
        'IS_EMPHASISED' => ($post['p_is_emphasised'] == 1),
    ), null, false, null, '.txt', 'text'));

    if ($return_html) {
        /*  The below works okay for Android. Unfortunately the Windows Mobile build has a very poor HTML rendered that can only do a handful of tags and entities.
        So instead we render as text and convert that to 'HTML'.

        $content = strip_attachments_from_comcode($content, true);

        // We need to simplify messy HTML as much as possible
        require_code('comcode_from_html');
        $content = force_clean_comcode($content);

        // FUDGE: Disable emoticons. Tapatalk will sub in those that it supports. If we don't do this it replaces them all with the normal smile emoticon using a dum replacer for any inline images
        $emoticon_map = get_tapatalk_to_composr_emoticon_map('perfect_matches');
        $bak = $GLOBALS['FORUM_DRIVER']->EMOTICON_CACHE;
        foreach ($emoticon_map as $tapatalk_code => $composr_code) {
            unset($GLOBALS['FORUM_DRIVER']->EMOTICON_CACHE[$composr_code]);
        }
        $emoticon_map = get_tapatalk_to_composr_emoticon_map('missing_from_composr');
        $content = str_replace(array_values($emoticon_map), array_keys($emoticon_map), $content); // Map Composr ones back to Tapatalk ones

        $post_tempcode = comcode_to_tempcode($content, $post['p_poster'], false, 60, null, $GLOBALS['FORUM_DB']);

        $GLOBALS['FORUM_DRIVER']->EMOTICON_CACHE = $bak;

        $content = $post_tempcode->evaluate();
        $content = trim(preg_replace('#[ \t]+#', ' ', preg_replace('#[ \t]*\n+#', ' ', $content))); // Strip line-breaks, as "quasi-HTML" may be used

        // No inline images allowed unless they are [img] tags (Tapatalk turns them into emoticons)
        $content = preg_replace('#<img[^>]* src="([^">]*)"[^>]+ alt="([^">]+)"[^>]+>#', '[img="$2"]$1[/img]', $content);
        $content = preg_replace('#<img[^>]* alt="([^">]+)"[^>]+ src="([^">]*)"[^>]+>#', '[img="$1"]$2[/img]', $content);
        $content = preg_replace('#<img[^>]* src="([^">]*)"[^>]+>#', '[img]$1[/img]', $content);

        // Take out any filename alt-text
        $content = preg_replace('#\[img( param)?="(C:\\\\fakepath\\\\|IMG|PC|DCP|SBCS|DSC|PIC)[^"]*"\](.*)\[/img\]#Us', '[img]$3[/img]', $content);

        // We don't want to have hidden text, must be made visible
        $content = preg_replace('#\[img( param)?="([^"]+)"\](.*)\[/img\]#Us', '$2:' . "\n" . '[img]$3[/img]', $content);
        */

        $content = tapatalk_strip_comcode($content);
        $content = nl2br(htmlspecialchars($content, ENT_NOQUOTES, get_charset()));
        $content = preg_replace('#\[color="[^"\[\]]"\](.*)\[/color\]#is', '<font color="\1">\2</font>', $content);

        $comcode_remap = array(
            '#\[b\]#' => '<b>',
            '#\[/b\]#' => '</b>',
            '#\[i\]#' => '<i>',
            '#\[/i\]#' => '</i>',
            '#\[u\]#' => '<u>',
            '#\[/u\]#' => '</u>',
            '#\[color="?([^\[\]]*)"?\]#' => '<font color="\1">',
            '#\[/color\]#' => '</font>',
        );
        $content = preg_replace(array_keys($comcode_remap), array_values($comcode_remap), $content);
    } else {
        $content = tapatalk_strip_comcode($content);
    }

    return $content;
}

/**
 * Get attachments details.
 *
 * @param  ?AUTO_LINK $post_id Post ID (null: Use attachment ID)
 * @param  ?AUTO_LINK $attachment_id Attachment ID (null: Use post ID)
 * @param  boolean $non_image_only Only do non-image attachments (because image ones are shown as [img] tags separately). Can only be used if $attachment_id is null.
 * @param  ?string $content Write content changes here (null: Don't)
 * @return array List of attachment details
 */
function get_post_attachments($post_id, $attachment_id = null, $non_image_only = false, &$content = null)
{
    require_code('files');
    require_code('images');

    $attachments = array();
    if (!is_null($post_id)) {
        $attachment_id_rows = $GLOBALS['FORUM_DB']->query_select('attachment_refs', array('a_id'), array('r_referer_id' => $post_id, 'r_referer_type' => 'cns_post'));
        foreach ($attachment_id_rows as $att) {
            $attachment_row = $GLOBALS['FORUM_DB']->query_select('attachments', array('*'), array('id' => $att['a_id']), '', 1);
            if (!isset($attachment_row[0])) {
                continue;
            }

            if ($non_image_only) {
                if (is_image($attachment_row[0]['a_original_filename'])) { // Already as [img] tag
                    if (!url_is_local($attachment_row[0]['a_url'])) {
                        if (!is_null($content)) {
                            $content = preg_replace('#\[img\][^\[\]]*' . preg_quote(find_script('attachment'), '#') . '\?id=' . strval($att['a_id']) . '[^\[\]]*\[\/img\]#U', '[img]' . $attachment_row[0]['a_url'] . '[/img]', $content);
                        }
                    }
                    continue;
                }
            }

            if (!is_image($attachment_row[0]['a_thumb_url'])) {
                continue; // Can't deal with this
            }

            $attachments[] = _get_attachment($attachment_row[0]);
        }
    } elseif (!is_null($attachment_id)) {
        $attachment_row = $GLOBALS['FORUM_DB']->query_select('attachments', array('a_url', 'a_thumb_url', 'a_original_filename', 'a_file_size'), array('id' => $attachment_id));
        if (!isset($attachment_row[0])) {
            return array();
        }

        $attachments[] = _get_attachment($attachment_row[0]);
    }
    return $attachments;
}

/**
 * Get details for an attachment.
 *
 * @param  array $attachment_row Attachment row
 * @return array Attachment details
 */
function _get_attachment($attachment_row)
{
    $url = $attachment_row['a_url'];
    if (url_is_local($url)) {
        $url = get_custom_base_url() . '/' . $url;
    }

    $ext = get_file_extension($attachment_row['a_original_filename']);
    if ($ext == 'pdf') {
        $content_type = 'pdf';
    } elseif (is_image($url)) {
        $content_type = 'image';
    } else {
        $content_type = 'other';
    }

    $thumb_url = $attachment_row['a_thumb_url'];
    if (url_is_local($thumb_url)) {
        $thumb_url = get_custom_base_url() . '/' . $thumb_url;
    }

    $url = find_script('attachment') . '?id=' . strval($attachment_row['id']);

    if (!is_image($attachment_row['a_url'])) {
        $url = $thumb_url; // Can't deal with this
    }

    return array(
        'id' => $attachment_row['id'],
        'filename' => $attachment_row['a_original_filename'],
        'content_type' => $content_type,
        'url' => $url,
        'thumbnail_url' => $thumb_url,
        'file_size' => $attachment_row['a_file_size'],
    );
}

/**
 * Render out a Tapatalk attachment to Mobiquo structure.
 *
 * @param  array $_attachments List of array attachments
 * @return array List of Mobiquo structures containing attachments
 */
function render_tapatalk_attachments($_attachments)
{
    $attachments = array();
    foreach ($_attachments as $att) {
        $attachments[] = mobiquo_val(array(
            'attachment_id' => mobiquo_val(strval($att['id']), 'string'),
            'filename' => mobiquo_val($att['filename'], 'base64'),
            'content_type' => mobiquo_val($att['content_type'], 'string'),
            'url' => mobiquo_val($att['url'], 'string'),
            'thumbnail_url' => mobiquo_val($att['thumbnail_url'], 'string'),
            'file_size' => mobiquo_val($att['file_size'], 'int'),
        ), 'struct');
    }
    return $attachments;
}

/**
 * Generate a shortened/cleaned post text from a post row.
 *
 * @param  array $post_row Post row
 * @param  boolean $topic_description Put a preference on topic description
 * @return string Post text
 */
function generate_shortened_post($post_row, $topic_description = false)
{
    // Generate short content, according to Tapatalk's rules

    require_code('xhtml');
    require_code('comcode_from_html');

    if (($topic_description) && ($post_row['t_description'] != '')) {
        $comcode = $post_row['t_description'];

        $short_content = static_evaluate_tempcode(comcode_to_tempcode($comcode, $post_row['p_poster']));
    } else {
        $comcode = get_translated_text($post_row['p_post'], $GLOBALS['FORUM_DB']);
        $comcode = strip_attachments_from_comcode($comcode);

        $short_content = static_evaluate_tempcode(comcode_to_tempcode($comcode, $post_row['p_poster']));
    }

    $short_content = xhtml_substr($short_content, 0, 200, false, true);
    $short_content = semihtml_to_comcode('[html]' . $short_content . '[/html]', true);
    $short_content = trim(cms_preg_replace_safe('#\s+#', ' ', $short_content));
    $short_content = trim(strip_comcode($short_content));

    return $short_content;
}

/**
 * Get the most recent moderation action for a resource.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  ID_TEXT $id Resource ID
 * @return ?array Tuple of moderation details (from f_moderator_logs table) (null: could not get)
 */
function get_last_moderation_details($type, $id)
{
    if (!has_actual_page_access(get_member(), 'admin_actionlog')) {
        return null;
    }

    $details = $GLOBALS['FORUM_DB']->query_select('f_moderator_logs', array('*'), array(
        'l_the_type' => (($type == 'topic') ? 'EDIT_TOPIC' : 'EDIT_POST'),
        'l_param_a' => $id,
    ), 'ORDER BY l_date_and_time DESC', 1);
    return isset($details[0]) ? $details[0] : null;
}

/**
 * Report a post.
 *
 * @param  AUTO_LINK $post_id Post ID
 * @param  string $reason Reason for action
 */
function report_post($post_id, $reason = '')
{
    if (!cns_may_report_post(get_member())) {
        access_denied('I_ERROR');
    }

    $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name(get_option('reported_posts_forum'));
    if (is_null($forum_id)) {
        warn_exit(do_lang_tempcode('NO_REPORTED_POST_FORUM'));
    }

    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

    $post_info = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t on t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post_id), '', 1);
    if (!array_key_exists(0, $post_info)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
    }

    if (!has_post_access($post_id, null, $post_info[0])) {
        access_denied('I_ERROR');
    }

    $topic_title = $post_info[0]['t_cache_first_title'];

    $report_title = $post_info[0]['p_title'];
    if ($report_title == '') {
        $report_title = $topic_title;
    }
    $title = do_lang('REPORTED_POST_TITLE', $report_title);

    // See if post already reported...
    $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_topics t LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts p ON p.id=t.t_cache_first_post_id', 't.id', array('p.p_title' => $title, 't.t_forum_id' => $forum_id));
    if (!is_null($topic_id)) {
        // Already a topic
        $first_post = false;
    } else { // New topic
        require_code('cns_topics_action');
        $topic_id = cns_make_topic($forum_id, '', '', 1, 1, 0, 0, 0, null, null, false);
        $first_post = true;
    }

    $poster = $post_info[0]['p_poster_name_if_guest'];

    $member = $poster;
    if (!is_guest($post_info[0]['p_poster'])) {
        $member = '[page="_SEARCH:members:view:' . strval($post_info[0]['p_poster']) . '"]' . $poster . '[/page]';
    }

    require_code('comcode_cleanup');
    $__post = comcode_censored_raw_code_access(get_translated_text($post_info[0]['p_post'], $GLOBALS['FORUM_DB']), $post_info[0]['p_poster']);
    $post = do_template('CNS_REPORTED_POST_FCOMCODE', array('_GUID' => '6e9a43a3503c357b52b724e11d3d4eef', 'POST_ID' => strval($post_id), 'MEMBER' => $member, 'TOPIC_TITLE' => $topic_title, 'POST' => $__post, 'POSTER' => $poster), null, false, null, '.txt', 'text');
    if ($reason != '') {
        $post->attach($reason);
    }

    require_code('cns_posts_action');
    cns_make_post($topic_id, $title, $post->evaluate(), 0, $first_post, 1, 0, null, null, null, null, null, null, null, false, true, null, true, $topic_title, 0, null, is_invisible(), true, false, false, null);

    decache('main_staff_checklist');
}

/**
 * Find relevant IDs from a URL.
 *
 * @param  URLPATH $url URL
 * @return ?array IDs in a map structure (null: not found)
 */
function get_id_by_url($url)
{
    $pagelink = url_to_page_link($url);
    if ($pagelink == '') {
        return null;
    }
    list(, $parts, $hash) = page_link_decode($pagelink);

    // A post?
    $matches = array();
    if (preg_match('#^\#?post_(\d+)#', $hash, $matches) != 0) {
        $post_id = intval($matches[1]);

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $result = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON p_topic_id=t.id', array('p_topic_id', 't_forum_id'), array('p.id' => $post_id), '', 1);
        if (isset($result[0])) {
            return array(
                'forum_id' => $result[0]['t_forum_id'],
                'topic_id' => $result[0]['p_topic_id'],
                'post_id' => $post_id,
            );
        }
    } // A topic?
    elseif (isset($parts['page']) && $parts['page'] == 'topicview' && isset($parts['id'])) {
        $test = is_numeric($parts['id']) ? $parts['id'] : $GLOBALS['SITE_DB']->query_select_value_if_there('url_id_monikers', 'm_resource_id', array('m_resource_page' => 'topicview', 'm_resource_type' => 'browse', 'm_moniker' => $parts['id']));
        if (!is_null($test)) {
            $result = $GLOBALS['FORUM_DB']->query_select('f_topics', array('t_cache_first_post_id', 't_forum_id'), array('id' => $test), '', 1);
            if (isset($result[0])) {
                return array(
                    'forum_id' => $result[0]['t_forum_id'],
                    'topic_id' => $test,
                    'post_id' => $result[0]['t_cache_first_post_id'],
                );
            }
        }
    }

    return null;
}
