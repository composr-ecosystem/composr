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

/**
 * Composr API helper class.
 */
class CMSPmWrite
{
    protected const TAPATALK_MESSAGE_NEW = 0;
    protected const TAPATALK_MESSAGE_REPLY = 1;
    protected const TAPATALK_MESSAGE_FORWARD = 2;

    /**
     * Report a private message.
     *
     * @param  AUTO_LINK $msg_id Message ID
     * @param  string $reason Reason for action
     */
    public function report_pm(int $msg_id, string $reason = '')
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if (!addon_installed('tickets')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('b70fb8be3ea75ebfb74c5ce58d866765')));
        }

        require_code('report_content');
        report_post($msg_id, '', is_invisible() ? 1 : 0);
    }

    /**
     * Create a private message.
     *
     * @param  array $user_name_list List of usernames
     * @param  string $subject Post subject
     * @param  string $message Post body
     * @param  integer $action Action
     * @param  ?AUTO_LINK $post_id Post ID to reply to or forward (null: new message so N/A)
     * @return AUTO_LINK New post ID
     */
    public function create_message(array $user_name_list, string $subject, string $message, int $action, ?int $post_id) : int
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_members2');
        require_code('cns_posts_action');
        require_code('cns_topics_action');

        cns_check_make_private_topic();

        if (empty($user_name_list)) {
            warn_exit(do_lang_tempcode('NO_RECIPIENTS_GIVEN'));
        }

        $from_id = get_member();

        $member_ids = [];
        foreach ($user_name_list as $username) {
            $to_member = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if (($to_member === null) || (is_guest($to_member))) {
                warn_exit(do_lang_tempcode('RECIPIENT_NOT_FOUND', escape_html($username)));
            }

            if (!cns_may_whisper($to_member)) {
                warn_exit(do_lang_tempcode('NO_PT_FROM_ALLOW'));
            }

            $member_ids[] = $to_member;
        }

        $first_new_post_id = null;

        $message = add_attachments_from_comcode($message, []);

        require_code('wordfilter');
        $subject = check_wordfilter($subject);

        switch ($action) {
            case self::TAPATALK_MESSAGE_NEW:
                if ($subject == '') {
                    $subject = do_lang('TAPATALK_HELLO');
                }

                foreach ($member_ids as $to_member) {
                    $new_topic_id = cns_make_topic(null, '', '', null, 1, 0, 0, $from_id, $to_member);
                    $new_post_id = cns_make_post($new_topic_id, $subject, $message, 0, true);
                    if ($first_new_post_id === null) {
                        $first_new_post_id = $new_post_id;
                    }
                }

                break;

            case self::TAPATALK_MESSAGE_REPLY:
                if ($post_id === null) {
                    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('e47b2bce2ec15d31945cb4fdadb40876')));
                }
                if (!has_post_access($post_id)) {
                    access_denied('I_ERROR');
                }

                foreach ($member_ids as $to_member) {
                    $topic_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_topic_id', ['id' => $post_id]);
                    $new_post_id = cns_make_post($topic_id, $subject, $message);
                    if ($first_new_post_id === null) {
                        $first_new_post_id = $new_post_id;
                    }
                }

                break;

            case self::TAPATALK_MESSAGE_FORWARD:
                if ($post_id === null) {
                    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('f1dd84b5ccb251d280f021e57c5c3835')));
                }
                $post_details = $GLOBALS['FORUM_DB']->query_select('f_posts', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $post_id], '', 1);
                if (!isset($post_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
                }
                if (!has_post_access($post_id, null, $post_details[0])) {
                    break;
                }

                $post = get_translated_text($post_details[0]['p_post'], $GLOBALS['FORUM_DB']);
                $poster = $GLOBALS['FORUM_DRIVER']->get_username($post_details[0]['p_posting_member']);
                $quote_content = '[quote="' . addslashes($poster) . '"]' . $post . "[/quote]\n";
                $_message = $quote_content . "\n\n" . $message;

                if ($subject == '') {
                    if ($post_details[0]['p_title'] == '') {
                        $subject = do_lang('TAPATALK_HELLO');
                    } else {
                        $subject = do_lang('QUOTE_MESSAGE_REPLY', $post_details[0]['p_title']);
                    }
                }

                foreach ($member_ids as $to_member) {
                    $new_topic_id = cns_make_topic(null, '', '', null, 1, 0, 0, $from_id, $to_member);
                    $new_post_id = cns_make_post($new_topic_id, $subject, $message, 0, true);
                    if ($first_new_post_id === null) {
                        $first_new_post_id = $new_post_id;
                    }
                }

                break;
        }

        return $first_new_post_id;
    }

    /**
     * Delete a private message.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function delete_message(int $post_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_posts_action3');

        $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_posts', 'p_topic_id', ['id' => $post_id]);
        if ($topic_id === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        cns_delete_posts_topic($topic_id, [$post_id], do_lang('REASON_TAPATALK_DELETING_POSTS')); // NB: Checks perms implicitly
    }

    /**
     * Mark a private message unread.
     *
     * @param  ?array $message_ids List of message IDs (null: all)
     */
    public function mark_pm_unread(?array $message_ids = null)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if ($message_ids === null) {
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $sql = 'SELECT id FROM ' . $table_prefix . 'f_topics t';
            $sql .= ' WHERE t_forum_id IS NULL';
            $sql .= ' AND (t_pt_from_member=' . strval(get_member()) . ' OR t_pt_to_member=' . strval(get_member()) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval(get_member()) . '))';
            $rows = $GLOBALS['FORUM_DB']->query($sql);
            $topic_ids = collapse_1d_complexity('id', $rows);
            foreach ($topic_ids as $topic_id) {
                $GLOBALS['FORUM_DB']->query_delete('f_read_logs', ['l_member_id' => get_member(), 'l_topic_id' => $topic_id], '', 1);
            }
        } else {
            foreach ($message_ids as $message_id) {
                $topic_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_topic_id', ['id' => $message_id]);
                $GLOBALS['FORUM_DB']->query_delete('f_read_logs', ['l_member_id' => get_member(), 'l_topic_id' => $topic_id], '', 1);
            }
        }
    }

    /**
     * Mark a private message read.
     *
     * @param  ?array $message_ids List of message IDs (null: all)
     */
    public function mark_pm_read(?array $message_ids = null)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if ($message_ids === null) {
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $sql = 'SELECT id FROM ' . $table_prefix . 'f_topics t';
            $sql .= ' WHERE t_forum_id IS NULL';
            $sql .= ' AND (t_pt_from_member=' . strval(get_member()) . ' OR t_pt_to_member=' . strval(get_member()) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval(get_member()) . '))';
            $rows = $GLOBALS['FORUM_DB']->query($sql);
            $topic_ids = collapse_1d_complexity('id', $rows);
            foreach ($topic_ids as $topic_id) {
                cns_ping_topic_read($topic_id);
            }
        } else {
            foreach ($message_ids as $message_id) {
                $topic_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_topic_id', ['id' => $message_id]);
                cns_ping_topic_read($topic_id);
            }
        }

        decache_private_topics(get_member());
    }
}
