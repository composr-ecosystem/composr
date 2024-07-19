<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: CMS.**/

/**
 * Composr API helper class.
 */
class CMSPmRead
{
    protected const UNREAD = 1;
    protected const READ = 2;
    protected const REPLIED = 3;

    /**
     * Get basic message box stats.
     *
     * @return array Tuple of details
     */
    public function get_box_info() : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $where = ['t_pt_to_member' => $member_id];
        if (addon_installed('validation')) {
            $where['t_validated'] = 1;
        }
        $inbox_total = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', $where);

        $inbox_unread_total = get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_INBOX);

        $where = ['t_pt_from_member' => $member_id];
        if (addon_installed('validation')) {
            $where['t_validated'] = 1;
        }
        $sent_total = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', $where);

        $sent_unread_total = get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_SENT);

        return [
            'inbox_total' => $inbox_total,
            'inbox_unread_total' => $inbox_unread_total,
            'sent_total' => $sent_total,
            'sent_unread_total' => $sent_unread_total,
        ];
    }

    /**
     * Get a private message message box (virtual inbox, constructed from private topics).
     *
     * @param  integer $box_id Box ID (a TAPATALK_MESSAGE_BOX_* constant)
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array Tuple of details
     */
    public function get_box(int $box_id, int $start, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        require_code('users2');

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_once(COMMON_CLASS_PATH_WRITE . '/post_write.php');

        if ($box_id == TAPATALK_MESSAGE_BOX_INBOX) {
            $lookup_key = 't_pt_to_member';
            $anti_lookup_key = 't_pt_from_member';
        } else {
            $lookup_key = 't_pt_from_member';
            $anti_lookup_key = 't_pt_to_member';
        }

        $member_id = get_member();

        $msgs_to = [];
        $total_unread_count = 0;

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $sql = 'SELECT *,t.id AS topic_id,p.id AS post_id';
        $sql .= ' FROM ' . $table_prefix . 'f_topics t JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id';
        if ($box_id == TAPATALK_MESSAGE_BOX_INBOX) {
            $sql .= ' WHERE (t_pt_to_member=' . strval($member_id) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval($member_id) . '))';
        } else {
            $sql .= ' WHERE t_pt_from_member=' . strval($member_id);
        }

        if (addon_installed('validation')) {
            $sql .= ' AND t_validated=1';
        }

        $sql .= ' ORDER BY p_time DESC,p.id DESC';

        $all_topics = $GLOBALS['FORUM_DB']->query($sql);

        $posts = [];
        foreach ($all_topics as $topic) {
            $topic_read_time = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_read_logs', 'l_time', ['l_member_id' => $member_id, 'l_topic_id' => $topic['topic_id']]);

            $table = 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id';

            $select = ['*', 'p.id AS post_id', 't.id AS topic_id'];

            $where = ['p_topic_id' => $topic['topic_id']];
            if (addon_installed('validation')) {
                $where['p_validated'] = 1;
            }

            $extra = '';
            if (!has_privilege($member_id, 'view_other_pt')) {
                $extra .= 'AND (p_whisper_to_member IS NULL OR p_posting_member=' . strval($member_id) . ' OR p_whisper_to_member=' . strval($member_id) . ')';
            }
            $extra .= 'ORDER BY p_time DESC,p.id DESC';

            $_posts = $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra);

            foreach ($_posts as $i => $post) {
                if (!has_post_access($post['post_id'], $member_id, $post)) {
                    continue;
                }

                $username = $GLOBALS['FORUM_DRIVER']->get_username($topic[$anti_lookup_key]);
                $msgs_to[$topic[$anti_lookup_key]] = [
                    'user_id' => $topic[$anti_lookup_key],
                    'username' => $username,
                ];

                $msg_state = $this->get_message_state($post, $topic, $member_id, $i, $_posts, $topic_read_time);
                if ($msg_state == self::UNREAD) {
                    $total_unread_count++;
                }

                $msg_from = $GLOBALS['FORUM_DRIVER']->get_username($post['p_posting_member']);

                $icon_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($post['p_posting_member']);

                $msg_subject = $post['p_title'];
                if ($msg_subject == '') {
                    $msg_subject .= do_lang('PRIVATE_MESSAGE_REPLY', $post['t_cache_first_title']);
                }

                $posts[] = [
                    'msg_id' => $post['post_id'],
                    'msg_state' => $msg_state,
                    'sent_date' => $post['p_time'],
                    'msg_from_id' => $post['p_posting_member'],
                    'msg_from' => $msg_from,
                    'icon_url' => $icon_url,
                    'msg_subject' => $msg_subject,
                    'short_content' => generate_shortened_post($post, $post['post_id'] == $topic['t_cache_first_post_id']),
                    'is_online' => member_is_online($post['p_posting_member']),
                ];
            }
        }

        return [
            'total_message_count' => count($posts),
            'total_unread_count' => $total_unread_count,
            'posts' => array_slice($posts, $start, $max),
            'msg_to' => array_values($msgs_to),
        ];
    }

    /**
     * Get the read status of a private topic post.
     *
     * @param  array $post_details Post details
     * @param  array $topic_details Topic details
     * @param  MEMBER $member_id Member ID
     * @param  integer $pos Position (of viewable posts) in topic so far
     * @param  array $posts All viewable posts in topic
     * @param  ?TIME $topic_read_time When the topic was last read by the current member (null: not)
     * @return integer Read status (special Tapatalk code)
     */
    private function get_message_state(array $post_details, array $topic_details, int $member_id, int $pos, array $posts, ?int $topic_read_time) : int
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        for ($i = 0; $i < $pos; $i++) {
            if ($posts[$i]['p_posting_member'] == get_member()) {
                return self::REPLIED;
            }
        }

        // Too old to track
        if ($topic_details['t_cache_last_time'] < time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))) {
            return self::READ;
        }

        if (($topic_read_time !== null) && ($topic_read_time > $post_details['p_time'])) {
            return self::READ;
        }

        return self::UNREAD;
    }

    /**
     * Get a private message.
     *
     * @param  AUTO_LINK $message_id Post ID
     * @param  boolean $return_html Return HTML
     * @return array Map of post details
     */
    public function get_message(int $message_id, bool $return_html) : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id';

        $select = ['*', 'p.id AS post_id', 't.id AS topic_id'];

        $where = ['p.id' => $message_id];

        $msg_details = $GLOBALS['FORUM_DB']->query_select($table, $select, $where, '', 1);

        if (!isset($msg_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $post_row = $msg_details[0];

        if (!has_post_access($post_row['post_id'], null, $post_row)) {
            access_denied('I_ERROR');
        }

        $msg_to = [];
        $username = $GLOBALS['FORUM_DRIVER']->get_username($post_row['t_pt_to_member']);
        $msg_to[] = [
            'user_id' => $post_row['t_pt_to_member'],
            'username' => $username,
        ];

        $username = $GLOBALS['FORUM_DRIVER']->get_username($post_row['t_pt_from_member']);

        $icon_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($post_row['p_posting_member']);

        cns_ping_topic_read($post_row['p_topic_id']);

        $content = prepare_post_for_tapatalk($post_row, $return_html);

        $attachment_details = get_post_attachments($post_row['post_id'], null, true, $content);

        return [
            'msg_from_id' => $post_row['t_pt_from_member'],
            'msg_from' => $username,
            'icon_url' => $icon_url,
            'sent_date' => $post_row['p_time'],
            'msg_subject' => $post_row['p_title'],
            'text_body' => $content,
            'msg_to' => $msg_to,
            'attachments' => $attachment_details,
        ];
    }

    /**
     * Quote a private message.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @return array A pair: quote title, quote content
     */
    public function get_quote_pm(int $post_id) : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

        $post_object = new CMSPostRead();
        $post_details = $post_object->get_raw_post($post_id);

        // Add quote message for post
        $quote_title = do_lang('PRIVATE_MESSAGE_REPLY', $post_details['post_title']);
        $quote_content = '[quote="' . addslashes($post_details['post_username']) . '"]' . $post_details['post_content'] . "[/quote]\n";

        return [$quote_title, $quote_content];
    }
}
