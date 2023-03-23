<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Composr API helper class.
 */
class CMSPtWrite
{
    /**
     * Create a new private topic.
     *
     * @param  array $user_name_list List of usernames to open with
     * @param  string $subject Subject
     * @param  string $body Body
     * @param  array $attachment_ids List of attachment IDs
     * @return AUTO_LINK Topic ID of new topic
     */
    public function new_private_topic(array $user_name_list, string $subject, string $body, array $attachment_ids) : int
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_members2');
        require_code('cns_posts_action');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        cns_check_make_private_topic();

        if (empty($user_name_list)) {
            warn_exit(do_lang_tempcode('NO_RECIPIENTS_GIVEN'));
        }

        $from_id = get_member();

        $member_ids = [];
        foreach ($user_name_list as $username) {
            $to_member = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if ($to_member === null) {
                warn_exit(do_lang_tempcode('RECIPIENT_NOT_FOUND', escape_html($username)));
            }

            if (!cns_may_whisper($to_member)) {
                warn_exit(do_lang_tempcode('NO_PT_FROM_ALLOW'));
            }

            $member_ids[] = $to_member;
        }

        $to_member = array_shift($member_ids);

        $body = add_attachments_from_comcode($body, $attachment_ids);

        require_code('wordfilter');
        $subject = check_wordfilter($subject);

        $new_topic_id = cns_make_topic(null, '', '', null, 1, 0, 0, $from_id, $to_member);
        cns_make_post($new_topic_id, $subject, $body, 0, true);

        // Invite more members
        foreach ($member_ids as $to_member) {
            cns_invite_to_pt($to_member, $new_topic_id);
        }

        return $new_topic_id;
    }

    /**
     * Add a reply to a private topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  string $subject Subject
     * @param  string $body Body
     * @param  array $attachment_ids List of attachment IDs
     * @return AUTO_LINK Post ID of new reply
     */
    public function reply_private_topic(int $topic_id, string $subject, string $body, array $attachment_ids) : int
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_posts_action');

        $body = add_attachments_from_comcode($body, $attachment_ids);

        require_code('wordfilter');
        $subject = check_wordfilter($subject);

        $new_post_id = cns_make_post($topic_id, $subject, $body); // NB: Checks perms implicitly

        return $new_post_id;
    }

    /**
     * Invite participants to a private topic.
     *
     * @param  array $user_name_list List of usernames to invite
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  string $reason Reason (may be blank)
     */
    public function invite_participants(array $user_name_list, int $topic_id, string $reason)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_topics_action2');

        $member_ids = [];
        foreach ($user_name_list as $username) {
            $to_member = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if ($to_member === null) {
                warn_exit(do_lang_tempcode('RECIPIENT_NOT_FOUND', escape_html($username)));
            }

            $member_ids[] = $to_member;
        }

        foreach ($member_ids as $to_member) {
            cns_invite_to_pt($to_member, $topic_id);
        }

        if (!empty($reason)) {
            $body = do_lang('TAPATALK_INVITED_TO_PT', implode(', ', $user_name_list), $reason);
            cns_make_post($topic_id, '', $body); // NB: Checks perms implicitly
        }
    }

    /**
     * Delete a private topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     */
    public function delete_private_topic(int $topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('cns_topics_action2');

        $topic_info = $GLOBALS['FORUM_DB']->query_select('f_topics', ['t_pt_from', 't_pt_to', 't_pt_from_category', 't_pt_to_category'], ['id' => $topic_id], '', 1);
        if (!array_key_exists(0, $topic_info)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }

        if ($topic_info[0]['t_pt_from'] == get_member()) {
            $us = 't_pt_from_category';
            $them = 't_pt_to_category';
        } elseif ($topic_info[0]['t_pt_to'] == get_member()) {
            $us = 't_pt_to_category';
            $them = 't_pt_from_category';
        } else {
            // Moderator deleting, so not categorising under a particular member, and reversing points
            cns_delete_topic($topic_id, do_lang('REASON_TAPATALK_DELETING_TOPIC'), null, true, true); // NB: Checks perms implicitly
            return;
        }

        if ((get_option('delete_trashed_pts') == '1') && ($topic_info[0][$them] == do_lang('TRASH'))) {
            cns_delete_topic($topic_id, do_lang('REASON_TAPATALK_DELETING_TOPIC'));
        } else {
            $GLOBALS['FORUM_DB']->query_update('f_topics', [$us => do_lang('TRASH')], ['id' => $topic_id], '', 1);
        }
    }

    /**
     * Mark a private topic unread.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     */
    public function mark_private_topic_unread(int $topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $GLOBALS['FORUM_DB']->query_delete('f_read_logs', ['l_member_id' => get_member(), 'l_topic_id' => $topic_id], '', 1);
    }

    /**
     * Mark a private topic read.
     *
     * @param  ?AUTO_LINK $topic_id Topic ID (null: all private topics)
     */
    public function mark_private_topic_read(?int $topic_id = null)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if ($topic_id === null) {
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $sql = 'SELECT id FROM ' . $table_prefix . 'f_topics t';
            $sql .= ' WHERE t_forum_id IS NULL';
            $sql .= ' AND (t_pt_from=' . strval(get_member()) . ' OR t_pt_to=' . strval(get_member()) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval(get_member()) . '))';
            $rows = $GLOBALS['FORUM_DB']->query($sql);
            $topic_ids = collapse_1d_complexity('id', $rows);
            foreach ($topic_ids as $topic_id) {
                cns_ping_topic_read($topic_id);
            }
        } else {
            cns_ping_topic_read($topic_id);
        }

        decache_private_topics(get_member());
    }
}
