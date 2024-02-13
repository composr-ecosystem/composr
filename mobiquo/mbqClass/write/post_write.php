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
class CMSPostWrite
{
    /**
     * Report a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @param  string $reason Reason for action
     */
    public function report_post(int $post_id, string $reason = '')
    {
        cms_verify_parameters_phpdoc();

        if (!addon_installed('tickets')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('report_content');
        report_post($post_id, $reason, is_invisible() ? 1 : 0);
    }

    /**
     * Reply to a post.
     *
     * @param  AUTO_LINK $forum_id Forum ID
     * @param  AUTO_LINK $topic_id Topic ID
     * @param  string $title Title
     * @param  string $post Post body
     * @param  array $attachment_ids List of attachment IDs
     * @param  boolean $return_html Whether to return HTML for posts rather than plain text
     * @return mixed Mobiquo structure of post
     */
    public function reply_post(int $forum_id, int $topic_id, string $title, string $post, array $attachment_ids, bool $return_html)
    {
        cms_verify_parameters_phpdoc();

        require_code('cns_posts_action');

        $member_id = get_member();

        $post = add_attachments_from_comcode($post, $attachment_ids);

        require_code('wordfilter');
        $title = check_wordfilter($title);

        $post_id = cns_make_post($topic_id, $title, $post, 0, false, null, 0, null, null, null, $member_id, null, null, null, true, true, $forum_id); // NB: Checks perms implicitly

        return render_post_to_tapatalk($post_id, $return_html, null, RENDER_POST_RESULT_TRUE);
    }

    /**
     * Edit a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @param  string $title Title
     * @param  string $post Post body
     * @param  array $attachment_ids List of attachment IDs
     * @param  boolean $return_html Whether to return HTML for posts rather than plain text
     * @param  string $reason Reason for action
     * @return mixed Mobiquo structure of post
     */
    public function edit_post(int $post_id, string $title, string $post, array $attachment_ids, bool $return_html, string $reason = '')
    {
        cms_verify_parameters_phpdoc();

        require_code('cns_posts_action3');

        if (!empty($attachment_ids)) {
            $post = strip_attachments_from_comcode($post);
            $post = add_attachments_from_comcode($post, $attachment_ids);
        }

        $mark_as_edited = (get_option('mark_as_edited') == '1');
        $mark_unread = (get_option('after_edit_mark_unread') == '1');

        // If it was submitted in raw HTML (due to HTML being edited), we may need to convert back to Comcode
        if ((strpos($post, '[html') === false) && (preg_match('#</(span|div|blockquote|h\d|font)>#', $post) != 0)) {
            require_code('comcode_from_html');
            $post = semihtml_to_comcode(nl2br($post));
        }

        $validated = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_validated', ['id' => $post_id]);
        cns_edit_post($post_id, $validated, $title, $post, 0, 0, null, $mark_as_edited, $mark_unread, $reason); // NB: Checks perms implicitly

        return render_post_to_tapatalk($post_id, $return_html, null, RENDER_POST_RESULT_TRUE);
    }
}
