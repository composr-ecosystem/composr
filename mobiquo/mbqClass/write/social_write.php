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
class CMSSocialWrite
{
    /**
     * Place a thank (points) on a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function thank_post(int $post_id)
    {
        cms_verify_parameters_phpdoc();

        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $post_id], '', 1);
        if (!isset($post_rows[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $user_id = $post_rows[0]['p_poster'];
        if ($user_id == get_member()) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!has_post_access($post_id, null, $post_rows[0])) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('points2');

        points_transact(get_member(), $user_id, do_lang('TAPATALK_THANK_POST', strval($post_id)), intval(get_option('points_for_thanking')), null, 0, true, 0, 'mobiquo', 'thank', strval($post_id));
    }

    /**
     * Set a friendship.
     *
     * @param  MEMBER $user_id Member to set on
     */
    public function follow(int $user_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if (!addon_installed('chat')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('chat2');
        friend_add(get_member(), $user_id);
    }

    /**
     * Remove a friendship.
     *
     * @param  MEMBER $user_id Member to remove on
     */
    public function unfollow(int $user_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        if (!addon_installed('chat')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('chat2');
        friend_remove(get_member(), $user_id);
    }

    /**
     * Like a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function like_post(int $post_id)
    {
        cms_verify_parameters_phpdoc();

        $this->set_post_rating($post_id, 10);
    }

    /**
     * Unlike a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function unlike_post(int $post_id)
    {
        cms_verify_parameters_phpdoc();

        $this->set_post_rating($post_id, null);
    }

    /**
     * Set a post rating.
     *
     * @param  AUTO_LINK $post_id Post ID
     * @param  ?INTEGER $rating The rating (null: unrate)
     */
    protected function set_post_rating(int $post_id, $rating)
    {
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $post_id], '', 1);
        if (!isset($post_rows[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }

        $user_id = $post_rows[0]['p_poster'];
        if ($user_id == get_member()) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!has_post_access($post_id, null, $post_rows[0])) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        $forum_id = $post_rows[0]['p_cache_forum_id'];
        $content_url = $GLOBALS['FORUM_DRIVER']->post_url($post_id, $forum_id);

        require_code('feedback');
        actualise_specific_rating($rating, 'topicview', get_member(), 'post', '', strval($post_id), $content_url, null);
    }
}
