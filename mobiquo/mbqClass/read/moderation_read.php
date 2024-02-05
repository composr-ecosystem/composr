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
class CMSModerationRead
{
    /**
     * Load up some topics needing moderation.
     *
     * @param  integer $start Start
     * @param  integer $max Max
     * @return ~array A pair: total topics, topics (false: error)
     */
    public function get_topics_needing_moderation(int $start, int $max)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        if (!addon_installed('validation')) {
            return [0, []];
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $where = ['t_validated' => 0];

        $total_topic_num = $GLOBALS['FORUM_DB']->query_select_value('f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id', 'COUNT(*)', $where);

        $_topics = $GLOBALS['FORUM_DB']->query_select(
            'f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id',
            ['*', 't.id AS topic_id', 'f.id AS forum_id', 'p.id AS post_id'],
            $where,
            'ORDER BY t_cache_first_time DESC',
            $max,
            $start
        );
        $topics = [];
        foreach ($_topics as $topic) {
            $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_MODERATED_BY);
        }

        return [$total_topic_num, $topics];
    }

    /**
     * Load up some posts needing moderation.
     *
     * @param  integer $start Start
     * @param  integer $max Max
     * @return ~array A pair: total posts, posts (false: error)
     */
    public function get_posts_needing_moderation(int $start, int $max)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return false;
        }

        if (!addon_installed('validation')) {
            return [0, []];
        }

        $where = ['p_validated' => 0];

        $total_post_num = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'COUNT(*)', $where);

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id';

        $select = ['*', 'p.id AS post_id', 't.id AS topic_id', 'f.id AS forum_id'];

        $extra = '';
        if (!has_privilege(get_member(), 'view_other_pt')) {
            $extra .= ' AND (p_intended_solely_for IS NULL OR p_intended_solely_for=' . strval(get_member()) . ' OR p_poster=' . strval(get_member()) . ')';
        }
        $extra .= ' ORDER BY p_time DESC,p.id DESC';

        $_posts = $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra, $max, $start);
        $posts = [];
        foreach ($_posts as $post) {
            $posts[] = render_post_to_tapatalk($post['post_id'], false, $post, RENDER_POST_SHORT_CONTENT | RENDER_POST_MODERATED_BY);
        }

        return [$total_post_num, $posts];
    }
}
