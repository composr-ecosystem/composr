<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class CMSSocialWrite
{
    /**
     * Place a thank (points) on a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function thank_post($post_id)
    {
        cms_verify_parameters_phpdoc();

        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post_id), '', 1);
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
        give_points(intval(get_option('points_for_thanking')), $user_id, get_member(), do_lang('TAPATALK_THANK_POST', strval($post_id)));
    }

    /**
     * Set a friendship.
     *
     * @param  MEMBER $user_id Member to set on.
     */
    public function follow($user_id)
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
     * @param  MEMBER $user_id Member to remove on.
     */
    public function unfollow($user_id)
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
    public function like_post($post_id)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post_id), '', 1);
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
        actualise_specific_rating(10, 'topicview', get_member(), 'post', '', strval($post_id), $content_url, null);
    }

    /**
     * Unlike a post.
     *
     * @param  AUTO_LINK $post_id Post ID
     */
    public function unlike_post($post_id)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $post_rows = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', array('*', 'p.id AS post_id', 't.id AS topic_id'), array('p.id' => $post_id), '', 1);
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
        actualise_specific_rating(null, 'topicview', get_member(), 'post', '', strval($post_id), $content_url, null);
    }
}
