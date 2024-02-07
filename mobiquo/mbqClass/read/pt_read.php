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

/*EXTRA FUNCTIONS: CMS.**/

/**
 * Composr API helper class.
 */
class CMSPtRead
{
    /**
     * Get the current member's private topics.
     *
     * @param  integer $start Start position for topics
     * @param  integer $max Maximum topics to show
     * @return array Tuple of details
     */
    public function get_private_topics(int $start, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $sql = ' FROM ' . $table_prefix . 'f_topics t' . $GLOBALS['FORUM_DB']->prefer_index('f_topics', 'in_forum', false);
        $sql .= 'JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id';
        $sql .= ' WHERE (t_pt_from=' . strval(get_member()) . ' OR t_pt_to=' . strval(get_member()) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval(get_member()) . '))';
        $sql .= ' AND (t_pt_from<>' . strval(get_member()) . ' OR ' . db_string_not_equal_to('t_pt_from_category', do_lang('TRASH')) . ')';
        $sql .= ' AND (t_pt_to<>' . strval(get_member()) . ' OR ' . db_string_not_equal_to('t_pt_to_category', do_lang('TRASH')) . ')';
        $sql .= ' ORDER BY t_cache_last_time DESC,t.id DESC';

        $_topics = $GLOBALS['FORUM_DB']->query('SELECT *,t.id AS topic_id,p.id AS post_id' . $sql, $max, $start);
        $topics_count = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*)' . $sql);

        $topics = [];
        foreach ($_topics as $topic) {
            $p_time_low = db_function('COALESCE', ['(SELECT l_time FROM ' . $table_prefix . 'f_read_logs l WHERE l_topic_id=p.p_topic_id AND l_member_id=' . strval(get_member()) . ')', '0']);
            $extra = ' AND p_time>' . $p_time_low . ' AND p_time>' . strval(time() - (60 * 60 * 24 * intval(get_option('post_read_history_days'))));
            $unread_num = $GLOBALS['FORUM_DB']->query_select_value('f_posts p', 'COUNT(*)', ['p.p_topic_id' => $topic['topic_id']], $extra);

            $participants = get_topic_participants($topic['topic_id'], null, $topic);

            $can_upload = (cns_get_member_best_group_property(get_member(), 'max_attachments_per_post') > 0);

            $topics[] = [
                'topic_id' => $topic['topic_id'],
                'total_posts' => $topic['t_cache_num_posts'],
                'participant_count' => count($participants),
                'start_user_id' => $topic['t_cache_first_member_id'],
                'start_conv_time' => $topic['t_cache_first_time'],
                'last_user_id' => $topic['t_cache_last_member_id'],
                'last_conv_time' => $topic['t_cache_last_time'],
                'conv_subject' => $topic['t_cache_first_title'],
                'new_post' => ($unread_num > 0),
                'unread_num' => $unread_num,
                'can_invite' => true,
                'can_edit' => has_privilege(get_member(), 'moderate_private_topic'),
                'can_close' => has_privilege(get_member(), 'moderate_private_topic'),
                'can_upload' => $can_upload,
                'is_closed' => ($topic['t_is_open'] == 0),
                'delete_mode' => 1, // soft-delete mode only
                'participants' => $participants,
            ];
        }

        $can_upload = (cns_get_member_best_group_property(get_member(), 'max_attachments_per_post') > 0);

        return [
            'topics_count' => $topics_count,
            'unread_count' => get_num_unread_private_topics(),
            'can_upload' => $can_upload,
            'topics' => $topics,
        ];
    }

    /**
     * Get a private topic (details of it, and posts).
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  integer $start Start position for posts
     * @param  integer $max Maximum posts to show
     * @param  boolean $return_html Return HTML
     * @return array Tuple of details
     */
    public function get_private_topic(int $topic_id, int $start, int $max, bool $return_html) : array
    {
        cms_verify_parameters_phpdoc();

        require_code('users2');

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id', ['*', 't.id AS topic_id', 'p.id AS post_id'], ['t.id' => $topic_id], '', 1);
        if (!isset($topic_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }

        if (!cns_may_access_topic($topic_id, null, $topic_details[0])) {
            access_denied('I_ERROR');
        }

        $sql = '';
        $sql .= ' FROM ' . $table_prefix . 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id';
        $sql .= ' WHERE ' . tapatalk_get_topic_where($topic_id);
        $sql .= ' ORDER BY p_time,p.id';
        $_posts = $GLOBALS['FORUM_DB']->query('SELECT *,p.id AS post_id,p.p_topic_id AS topic_id' . $sql, $max, $start);
        $total_post_count = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*)' . $sql);

        $last_read_sql = db_function('COALESCE', ['(SELECT l_time FROM ' . $table_prefix . 'f_read_logs l WHERE l_topic_id=p.p_topic_id AND l_member_id=' . strval(get_member()) . ')', '0']);

        $extra = ' AND p_time>' . db_function('GREATEST', [
            strval(time() - (60 * 60 * 24 * intval(get_option('post_read_history_days')))),
            $last_read_sql,
        ]);
        $unread_num = $GLOBALS['FORUM_DB']->query_select_value('f_posts p', 'COUNT(*)', ['p.p_topic_id' => $topic_id], $extra);

        $topic_read_time = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_read_logs', 'l_time', ['l_member_id' => get_member(), 'l_topic_id' => $topic_id]);

        $posts = [];
        foreach ($_posts as $post) {
            $content = prepare_post_for_tapatalk($post, $return_html);

            $attachments = get_post_attachments($post['post_id'], null, true, $content);

            $is_unread = ($topic_read_time === null) || ($post['p_time'] > $topic_read_time);

            $posts[] = [
                'msg_id' => $post['post_id'],
                'msg_content' => $content,
                'msg_author_id' => $post['p_poster'],
                'is_unread' => $is_unread,
                'is_online' => member_is_online($post['p_poster']),
                'has_left' => false,
                'post_time' => $post['p_time'],
                'new_post' => $is_unread,
                'attachments' => $attachments,
            ];
        }

        $participants = get_topic_participants($topic_id, null, $topic_details[0]);

        $can_upload = (cns_get_member_best_group_property(get_member(), 'max_attachments_per_post') > 0);

        cns_ping_topic_read($topic_id);

        return [
            'topic_id' => $topic_id,
            'conv_title' => $topic_details[0]['t_cache_first_title'],
            'participant_count' => count($participants),
            'start_user_id' => $topic_details[0]['t_cache_first_member_id'],
            'start_conv_time' => $topic_details[0]['t_cache_first_time'],
            'last_user_id' => $topic_details[0]['t_cache_last_member_id'],
            'last_conv_time' => $topic_details[0]['t_cache_last_time'],
            'total_posts' => $total_post_count,
            'new_post' => ($unread_num > 0),
            'can_invite' => true,
            'can_edit' => has_privilege(get_member(), 'moderate_private_topic'),
            'can_close' => has_privilege(get_member(), 'moderate_private_topic'),
            'can_upload' => $can_upload,
            'is_closed' => ($topic_details[0]['t_is_open'] == 0),
            'delete_mode' => 1, // soft-delete mode only
            'participants' => $participants,
            'posts' => $posts,
        ];
    }

    /**
     * Get Comcode for quoting a post within a private topic.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @return string Body for new quoted post
     */
    public function get_quote_for_private_topic(int $post_id) : string
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

        $post_object = new CMSPostRead();
        $post_details = $post_object->get_raw_post($post_id);

        return '[quote="' . addslashes($post_details['post_username']) . '"]' . $post_details['post_content'] . "[/quote]\n";
    }
}
