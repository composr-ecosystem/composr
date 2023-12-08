ptrad<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
class CMSPmRead
{
    const UNREAD = 1;
    const READ = 2;
    const REPLIED = 3;

    /**
     * Get basic message box stats.
     *
     * @return array Tuple of details
     */
    public function get_box_info()
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $where = array('t_pt_to' => $member_id);
        if (addon_installed('unvalidated')) {
            $where['t_validated'] = 1;
        }
        $inbox_total = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', $where);

        $inbox_unread_total = get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_INBOX);

        $where = array('t_pt_from' => $member_id);
        if (addon_installed('unvalidated')) {
            $where['t_validated'] = 1;
        }
        $sent_total = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', $where);

        $sent_unread_total = get_num_unread_private_topics(TAPATALK_MESSAGE_BOX_SENT);

        return array(
            'inbox_total' => $inbox_total,
            'inbox_unread_total' => $inbox_unread_total,
            'sent_total' => $sent_total,
            'sent_unread_total' => $sent_unread_total,
        );
    }

    /**
     * Get a private message message box (virtual inbox, constructed from private topics).
     *
     * @param  integer $box_id Box ID (a TAPATALK_MESSAGE_BOX_* constant)
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array Tuple of details
     */
    public function get_box($box_id, $start, $max)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_once(COMMON_CLASS_PATH_WRITE . '/post_write.php');

        if ($box_id == TAPATALK_MESSAGE_BOX_INBOX) {
            $lookup_key = 't_pt_to';
            $anti_lookup_key = 't_pt_from';
        } else {
            $lookup_key = 't_pt_from';
            $anti_lookup_key = 't_pt_to';
        }

        $member_id = get_member();

        $msgs_to = array();
        $total_unread_count = 0;

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $sql = 'SELECT *,t.id AS topic_id,p.id AS post_id';
        $sql .= ' FROM ' . $table_prefix . 'f_topics t JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id';
        if ($box_id == TAPATALK_MESSAGE_BOX_INBOX) {
            $sql .= ' WHERE (t_pt_to=' . strval($member_id) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval($member_id) . '))';
        } else {
            $sql .= ' WHERE t_pt_from=' . strval($member_id);
        }

        if (addon_installed('unvalidated')) {
            $sql .= ' AND t_validated=1';
        }

        $sql .= ' ORDER BY p_time DESC,p.id DESC';

        $all_topics = $GLOBALS['FORUM_DB']->query($sql);

        $posts = array();
        foreach ($all_topics as $topic) {
            $topic_read_time = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_read_logs', 'l_time', array('l_member_id' => $member_id, 'l_topic_id' => $topic['topic_id']));

            $table = 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id';

            $select = array('*', 'p.id AS post_id', 't.id AS topic_id');

            $where = array('p_topic_id' => $topic['topic_id']);
            if (addon_installed('unvalidated')) {
                $where['p_validated'] = 1;
            }

            $extra = '';
            if (!has_privilege($member_id, 'view_other_pt')) {
                $extra .= 'AND (p_intended_solely_for IS NULL OR p_poster=' . strval($member_id) . ' OR p_intended_solely_for=' . strval($member_id) . ')';
            }
            $extra .= 'ORDER BY p_time DESC,p.id DESC';

            $_posts = $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra);

            foreach ($_posts as $i => $post) {
                if (!has_post_access($post['post_id'], $member_id, $post)) {
                    continue;
                }

                $username = $GLOBALS['FORUM_DRIVER']->get_username($topic[$anti_lookup_key]);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }
                $msgs_to[$topic[$anti_lookup_key]] = array(
                    'user_id' => $topic[$anti_lookup_key],
                    'username' => $username,
                );

                $msg_state = $this->get_message_state($post, $topic, $member_id, $i, $_posts, $topic_read_time);
                if ($msg_state == self::UNREAD) {
                    $total_unread_count++;
                }

                $msg_from = $GLOBALS['FORUM_DRIVER']->get_username($post['p_poster']);
                if (is_null($msg_from)) {
                    $msg_from = do_lang('UNKNOWN');
                }

                $icon_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($post['p_poster']);

                $msg_subject = $post['p_title'];
                if ($msg_subject == '') {
                    $msg_subject .= do_lang('PRIVATE_MESSAGE_REPLY', $post['t_cache_first_title']);
                }

                $posts[] = array(
                    'msg_id' => $post['post_id'],
                    'msg_state' => $msg_state,
                    'sent_date' => $post['p_time'],
                    'msg_from_id' => $post['p_poster'],
                    'msg_from' => $msg_from,
                    'icon_url' => $icon_url,
                    'msg_subject' => $msg_subject,
                    'short_content' => generate_shortened_post($post, $post['post_id'] == $topic['t_cache_first_post_id']),
                    'is_online' => is_member_online($post['p_poster']),
                );
            }
        }

        return array(
            'total_message_count' => count($posts),
            'total_unread_count' => $total_unread_count,
            'posts' => array_slice($posts, $start, $max),
            'msg_to' => array_values($msgs_to),
        );
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
    private function get_message_state($post_details, $topic_details, $member_id, $pos, $posts, $topic_read_time)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        for ($i = 0; $i < $pos; $i++) {
            if ($posts[$i]['p_poster'] == get_member()) {
                return self::REPLIED;
            }
        }

        // Too old to track
        if ($topic_details['t_cache_last_time'] < time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))) {
            return self::READ;
        }

        if ($topic_read_time !== null && $topic_read_time > $post_details['p_time']) {
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
    public function get_message($message_id, $return_html)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id';

        $select = array('*', 'p.id AS post_id', 't.id AS topic_id');

        $where = array('p.id' => $message_id);

        $msg_details = $GLOBALS['FORUM_DB']->query_select($table, $select, $where, '', 1);

        if (!isset($msg_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $post_row = $msg_details[0];

        if (!has_post_access($post_row['post_id'], null, $post_row)) {
            access_denied('I_ERROR');
        }

        $msg_to = array();
        $username = $GLOBALS['FORUM_DRIVER']->get_username($post_row['t_pt_to']);
        if (is_null($username)) {
            $username = do_lang('UNKNOWN');
        }
        $msg_to[] = array(
            'user_id' => $post_row['t_pt_to'],
            'username' => $username,
        );

        $username = $GLOBALS['FORUM_DRIVER']->get_username($post_row['t_pt_from']);
        if (is_null($username)) {
            $username = do_lang('UNKNOWN');
        }

        $icon_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($post_row['p_poster']);

        cns_ping_topic_read($post_row['p_topic_id']);

        $content = prepare_post_for_tapatalk($post_row, $return_html);

        $attachment_details = get_post_attachments($post_row['post_id'], null, true, $content);

        return array(
            'msg_from_id' => $post_row['t_pt_from'],
            'msg_from' => $username,
            'icon_url' => $icon_url,
            'sent_date' => $post_row['p_time'],
            'msg_subject' => $post_row['p_title'],
            'text_body' => $content,
            'msg_to' => $msg_to,
            'attachments' => $attachment_details,
        );
    }

    /**
     * Quote a private message.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @return array A pair: quote title, quote content
     */
    public function get_quote_pm($post_id)
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

        return array($quote_title, $quote_content);
    }
}
