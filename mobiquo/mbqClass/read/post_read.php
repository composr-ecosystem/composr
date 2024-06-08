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
class CMSPostRead
{
    /**
     * Get Comcode for quoting a post.
     *
     * @param  array $post_ids Post IDs
     * @return array Tuple of result details
     */
    public function get_quote_post(array $post_ids) : array
    {
        cms_verify_parameters_phpdoc();

        $quote_title = null;
        $quote_content = '';

        foreach ($post_ids as $post_id) {
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t on t.id=p.p_topic_id', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $post_id], '', 1);

            if (!isset($post_details[0])) {
                continue;
            }

            if (!has_post_access($post_id, null, $post_details[0])) {
                continue;
            }

            if ($quote_title === null) {
                $quote_title = do_lang('QUOTE_MESSAGE_REPLY', $post_details[0]['p_title']);
            }

            $poster = $post_details[0]['p_poster_name_if_guest'];
            if ($poster == '') {
                $poster = $GLOBALS['FORUM_DRIVER']->get_username($post_details[0]['p_posting_member']);
            }

            $quote_content .= '[quote="' . addslashes($poster) . '"]' . get_translated_text($post_details[0]['p_post'], $GLOBALS['FORUM_DB']) . "[/quote]\n";
        }

        return [$quote_title, $quote_content];
    }

    /**
     * Load up basic details of a post, for editing purposes.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @return array Post
     */
    public function get_raw_post(int $post_id) : array
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t on t.id=p.p_topic_id', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $post_id], '', 1);

        if (!isset($post_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $post = $post_details[0];
        if (!has_post_access($post_id, null, $post)) {
            access_denied('I_ERROR');
        }

        $edit_reason = '';
        if (has_actual_page_access(get_member(), 'admin_actionlog')) {
            $_edit_reason = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_moderator_logs', 'l_reason', ['l_the_type' => 'EDIT_POST', 'l_param_a' => strval($post_id)]);
            if ($_edit_reason !== null) {
                $edit_reason = $_edit_reason;
            }
        }

        $content = strip_attachments_from_comcode(get_translated_text($post['p_post'], $GLOBALS['FORUM_DB']));

        return [
            'post_id' => $post_id,
            'post_title' => $post['p_title'],
            'post_username' => $GLOBALS['FORUM_DRIVER']->get_username($post['p_posting_member']),
            'post_content' => $content,
            'edit_reason' => $edit_reason,
            'attachments' => get_post_attachments($post_id),
        ];
    }

    /**
     * Render a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  ?integer $start Start position for topic post retrieval (null: return no posts)
     * @param  ?integer $max Maximum topic posts retrieved (null: return no posts)
     * @param  boolean $return_html Whether to return HTML for post data
     * @param  ?AUTO_LINK $position Post position to scroll to (null: N/A)
     *
     * @return object Mobiquo array
     */
    public function get_topic(int $topic_id, ?int $start, ?int $max, bool $return_html, ?int $position = null) : object
    {
        cms_verify_parameters_phpdoc();

        if (!cns_may_access_topic($topic_id)) {
            access_denied('I_ERROR');
        }

        cns_ping_topic_read($topic_id);

        return render_topic_to_tapatalk($topic_id, $return_html, $start, $max, null, 0, $position);
    }
}
