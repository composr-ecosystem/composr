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

/**
 * Composr API helper class.
 */
class CMSModerationWrite
{
    /**
     * Pin a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @return boolean Success status (failure always due to access denied)
     */
    public function stick_topic($topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_edit_topic($topic_id, null, null, null, null, 1, null, null, ''); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Unpin a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @return boolean Success status (failure always due to access denied)
     */
    public function unstick_topic($topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_edit_topic($topic_id, null, null, null, null, 0, null, null, ''); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Close a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @return boolean Success status (failure always due to access denied)
     */
    public function close_topic($topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_edit_topic($topic_id, null, null, null, 0, null, null, null, ''); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Open a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @return boolean Success status (failure always due to access denied)
     */
    public function open_topic($topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_edit_topic($topic_id, null, null, null, 1, null, null, null, ''); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Delete a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  string $reason Reason for action
     * @return boolean Success status (failure always due to access denied)
     */
    public function delete_topic($topic_id, $reason = '')
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_delete_topic($topic_id, $reason); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Delete a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @param  string $reason Reason for action
     * @return boolean Success status (failure always due to access denied)
     */
    public function delete_post($post_id, $reason = '')
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_posts_action3');
        $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_posts', 'p_topic_id', array('id' => $post_id));
        if (is_null($topic_id)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }
        cns_delete_posts_topic($topic_id, array($post_id), $reason); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Move a topic to another forum.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  AUTO_LINK $to_forum_id Forum ID
     * @return boolean Success status (failure always due to access denied)
     */
    public function move_topic($topic_id, $to_forum_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        $from_forum_id = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 't_forum_id', array('id' => $topic_id));
        cns_move_topics($from_forum_id, $to_forum_id, array($topic_id)); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Rename a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  string $new_title New title
     * @return boolean Success status (failure always due to access denied)
     */
    public function rename_topic($topic_id, $new_title)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        require_code('cns_topics_action2');
        cns_edit_topic($topic_id, null, null, null, null, null, null, null, do_lang('REASON_TAPATALK_RENAMING_TOPIC'), $new_title); // NB: Checks perms implicitly
        return true;
    }

    /**
     * Move posts.
     *
     * @param  array $posts List of post IDs
     * @param  ?AUTO_LINK $to_topic_id Topic ID (null: moving to new topic)
     * @param  ?string $new_topic_title New title (null: moving to existing topic)
     * @param  ?AUTO_LINK $forum_id Forum ID (null: moving to existing topic)
     * @return ~AUTO_LINK ID of topic the posts have gone to (false: failure due to access denied)
     */
    public function move_posts($posts, $to_topic_id, $new_topic_title, $forum_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        // Group the posts up by topic
        $topics = array();
        foreach ($posts as $post_id) {
            $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_posts', 'p_topic_id', array('id' => $post_id));
            if ($topic_id !== null) {
                if (!isset($topics[$topic_id])) {
                    $topics[$topic_id] = array();
                }
                $topics[$topic_id][] = $post_id;
            }
        }

        // Move each post group
        foreach ($topics as $from_topic_id => $post_ids) {
            require_code('cns_posts_action3');
            cns_move_posts($from_topic_id, $to_topic_id, $post_ids, do_lang('REASON_TAPATALK_MOVING_POSTS'), $forum_id, true, $new_topic_title); // NB: Checks perms implicitly

            if ($to_topic_id === null) {
                $to_topic_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_topic_id', array('id' => $post_ids[0]));
            }
        }

        return $to_topic_id;
    }

    /**
     * Merge two topics.
     *
     * @param  AUTO_LINK $from_topic_id First topic
     * @param  AUTO_LINK $to_topic_id Second topic
     * @return boolean Success status (failure always due to access denied)
     */
    public function merge_topics($from_topic_id, $to_topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }
        if (!can_merge_topics($from_topic_id, $to_topic_id, get_member())) {
            access_denied('I_ERROR');
        }

        $post_ids = collapse_1d_complexity('id', $GLOBALS['FORUM_DB']->query_select('f_posts p', array('id', 'p.id AS post_id'), array('p_topic_id' => $from_topic_id)));
        if (empty($post_ids)) {
            warn_exit(do_lang_tempcode('CANNOT_MERGE_EMPTY_TOPIC'));
        }

        require_code('cns_posts_action3');
        cns_move_posts($from_topic_id, $to_topic_id, $post_ids, do_lang('REASON_TAPATALK_MERGING_TOPICS'));
        return true;
    }

    /**
     * Merge posts into one particular post (not another topic).
     *
     * @param  array $source_post_ids List of post IDs to merge
     * @param  AUTO_LINK $target_post_id Target post IDs
     * @return boolean Success status (failure always due to access denied)
     */
    public function merge_posts($source_post_ids, $target_post_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        $target_posts = $GLOBALS['FORUM_DB']->query_select('f_posts p', array('*', 'p.id AS post_id'), array('p.id' => $target_post_id), '', 1);
        if (!isset($target_posts[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }
        $target_post = $target_posts[0];

        // We will put all posts into an array that is sortable, then merge those

        $post = array();

        $key = str_pad(strval($target_post['p_time']), 15, '0', STR_PAD_LEFT) . '_' . strval($target_post['post_id']);
        $post[$key] = get_translated_text($target_post['p_post'], $GLOBALS['FORUM_DB']);

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $sql = 'SELECT *,id AS post_id FROM ' . $table_prefix . 'f_posts WHERE p_intended_solely_for IS NULL AND id IN (' . implode(',', array_map('strval', $source_post_ids)) . ')';
        $source_posts = ($source_post_ids == array()) ? array() : $GLOBALS['FORUM_DB']->query($sql);
        foreach ($source_posts as $source_post) {
            $key = str_pad(strval($source_post['p_time']), 15, '0', STR_PAD_LEFT) . '_' . strval($source_post['post_id']);
            $post[$key] = get_translated_text($source_post['p_post'], $GLOBALS['FORUM_DB']);
        }

        sort($post);

        $merged_post = implode("\n\n", $post);

        $GLOBALS['FORUM_DB']->query_update(
            'f_posts',
            lang_remap_comcode('p_post', $target_post['p_post'], $merged_post, $GLOBALS['FORUM_DB']),
            array('id' => $target_post_id),
            '',
            1
        );

        require_code('cns_posts_action3');
        cns_delete_posts_topic($target_post['p_topic_id'], $source_post_ids, do_lang('REASON_TAPATALK_DELETING_POSTS')); // NB: Checks perms implicitly

        return true;
    }

    /**
     * Approve/unapprove a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  boolean $approve True=Approve, False=Unapprove
     * @return boolean Success status (failure always due to access denied)
     */
    public function approve_topic($topic_id, $approve)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        // NB: Checks perms implicitly
        if ($approve) {
            require_code('cns_topics_action2');
            cns_edit_topic($topic_id, null, null, 1);
        } else {
            require_code('cns_topics_action2');
            cns_edit_topic($topic_id, null, null, 0);
        }

        return true;
    }

    /**
     * Approve/unapprove a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @param  boolean $approve True=Approve, False=Unapprove
     * @return boolean Success status (failure always due to access denied)
     */
    public function approve_post($post_id, $approve)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        $forum_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_cache_forum_id', array('id' => $post_id));

        if (!cns_may_moderate_forum($forum_id)) {
            access_denied('I_ERROR');
        }

        require_code('cns_general_action2');

        if ($approve) {
            require_code('cns_topics_action2');
            $GLOBALS['FORUM_DB']->query_update('f_posts', array('p_validated' => 1), array('id' => $post_id), '', 1);

            cns_mod_log_it('VALIDATE_POST', strval($post_id));
        } else {
            require_code('cns_topics_action2');
            $GLOBALS['FORUM_DB']->query_update('f_posts', array('p_validated' => 0), array('id' => $post_id), '', 1);

            cns_mod_log_it('UNVALIDATE_POST', strval($post_id));
        }

        return true;
    }

    /**
     * Ban a user.
     *
     * @param  ID_TEXT $username Username to ban
     * @param  boolean $delete_all_posts Whether to delete all posts also
     * @param  string $reason Reason for action
     * @param  ?integer $expires When probation should expire in days from now (null: permanent ban, not probation)
     * @return boolean Success status (failure always due to access denied)
     */
    public function ban_user($username, $delete_all_posts = false, $reason = '', $expires = null)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        if (!addon_installed('cns_warnings')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!cns_may_warn_members()) {
            access_denied('I_ERROR');
        }

        $user_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        if (($user_id === null) || (is_guest($user_id))) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'));
        }

        require_lang('cns_warnings');

        if (($delete_all_posts) && (!has_delete_permission('low', get_member(), $user_id, 'topics'))) {
            require_code('cns_posts_action3');
            $posts = collapse_1d_complexity('id', $GLOBALS['FORUM_DB']->query_select('f_posts p', array('p.id AS post_id'), array('p_poster' => $user_id), ' AND p_cache_forum_id IS NOT NULL'));

            // Group the posts up by topic
            $topics = array();
            foreach ($posts as $post_id) {
                $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_posts', 'p_topic_id', array('id' => $post_id));
                if ($topic_id !== null) {
                    if (!isset($topics[$topic_id])) {
                        $topics[$topic_id] = array();
                    }
                    $topics[$topic_id][] = $post_id;
                }
            }

            // Delete each post group
            foreach ($topics as $topic_id => $post_ids) {
                cns_delete_posts_topic($topic_id, $post_ids, $reason);
            }
        }

        if (is_null($expires)) {
            require_code('cns_members_action2');
            cns_ban_member($user_id);
        } else {
            $GLOBALS['FORUM_DB']->query_update('f_members', array('m_on_probation_until' => $expires), array('id' => $user_id), '', 1);

            require_code('cns_general_action2');
            cns_mod_log_it('PUT_ON_PROBATION', strval($user_id), $username, $reason);
        }

        return true;
    }

    /**
     * Unban a user.
     *
     * @param  MEMBER $user_id Member to unban
     * @return boolean Success status (failure always due to access denied)
     */
    public function unban_user($user_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        if (!addon_installed('cns_warnings')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!cns_may_warn_members()) {
            access_denied('I_ERROR');
        }

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
        if (is_null($username)) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'));
        }

        require_lang('cns_warnings');

        require_code('cns_members_action2');
        cns_unban_member($user_id);

        $on_probation_until = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_on_probation_until');
        if (!is_null($on_probation_until)) {
            $GLOBALS['FORUM_DB']->query_update('f_members', array('m_on_probation_until' => null), array('id' => $user_id), '', 1);

            require_code('cns_general_action2');
            cns_mod_log_it('REMOVE_FROM_PROBATION', strval($user_id), $username);
        }

        return true;
    }

    /**
     * Mark a member as a spammer.
     *
     * @param  MEMBER $user_id Member to mark as a spammer
     * @return boolean Success status (failure always due to access denied)
     */
    public function mark_as_spam($user_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        if (!cns_may_warn_members()) {
            access_denied('I_ERROR');
        }

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);
        if (is_null($username)) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'));
        }

        $ip = $GLOBALS['FORUM_DRIVER']->get_member_row_field($user_id, 'm_ip_address');

        $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($user_id);

        require_code('failure');
        require_code('failure_spammers');
        syndicate_spammer_report($ip, $username, $email, '', false);

        require_code('cns_general_action2');
        cns_mod_log_it('MARK_AS_SPAMMER', strval($user_id), $username);

        return true;
    }
}
