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
class CMSSearchRead
{
    /**
     * Search topics.
     *
     * @param  string $keywords Keywords
     * @param  integer $start Start position
     * @param  integer $max Max position
     * @param  ?MEMBER $userid Member ID (null: no filter)
     * @param  ?string $searchuser Username (null: no filter)
     * @param  ?AUTO_LINK $forumid Forum ID (null: no filter)
     * @param  ?boolean $titleonly Title only (null: no filter)
     * @param  ?TIME $searchtime Time since (null: no filter)
     * @param  ?array $only_in Only in these forums (null: no filter)
     * @param  ?array $not_in Not in these forums (null: no filter)
     * @return array A pair: total topics, topics
     */
    public function search_topics($keywords, $start, $max, $userid = null, $searchuser = null, $forumid = null, $titleonly = false, $searchtime = null, $only_in = null, $not_in = null)
    {
        cms_verify_parameters_phpdoc();

        require_code('database_search');
        $table_prefix = get_table_prefix();
        $boolean_operator = 'AND';

        $sql1 = ' FROM ' . $table_prefix . 'f_posts p';
        if ($keywords != '') {
            if (strpos(get_db_type(), 'mysql') !== false) {
                $sql1 .= ' FORCE INDEX (p_title)';
            }
        }
        $sql1 .= ' JOIN ' . $table_prefix . 'f_topics t ON t.t_cache_first_post_id=p.id LEFT JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE 1=1';
        $sql2 = ' FROM ' . $table_prefix . 'f_topics t';
        if ($keywords != '') {
            if (strpos(get_db_type(), 'mysql') !== false) {
                $sql2 .= ' FORCE INDEX (t_description)';
            }
        }
        $sql2 .= ' JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id LEFT JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE 1=1';

        $where = '';

        $where .= ' AND t_forum_id IN (' . get_allowed_forum_sql() . ')';

        if ($keywords != '') {
            list($w) = build_content_where($keywords, false, $boolean_operator);
            if ($w != '') {
                $sql1 .= ' AND ' . preg_replace('#\?#', 'p_title', $w);
                $sql2 .= ' AND ' . preg_replace('#\?#', 't_description', $w);
            }
        }

        if (addon_installed('unvalidated')) {
            $where .= ' AND t_validated=1';
        }

        if (!is_null($userid)) {
            $where .= ' AND t_cache_first_member_id=' . strval($userid);
        } elseif (!is_null($searchuser)) {
            $_userid = $GLOBALS['FORUM_DRIVER']->get_member_from_username($searchuser);
            if (is_null($_userid)) {
                warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($searchuser)));
            }
            $where .= ' AND t_cache_first_member_id=' . strval($_userid);
        }

        if (!is_null($forumid)) {
            $where .= ' AND t_forum_id=' . strval($forumid);
        }

        if (!is_null($searchtime)) {
            $where .= ' AND t_cache_last_time>' . strval(time() - $searchtime);
        }

        if (!is_null($only_in)) {
            if (count($only_in) == 0) {
                $where .= ' AND 1=0';
            } else {
                $where .= ' AND t_forum_id IN (' . implode(',', array_map('strval', array_map('intval', $only_in))) . ')';
            }
        }

        if (!is_null($not_in)) {
            if (count($not_in) == 0) {
                $where .= ' AND 1=1';
            } else {
                $where .= ' AND t_forum_id NOT IN (' . implode(',', array_map('strval', array_map('intval', $not_in))) . ')';
            }
        }

        $select = '*,f.id as forum_id,t.id AS topic_id,p.id AS post_id';

        $full_sql1 = 'SELECT ' . $select . $sql1 . $where;
        if ($keywords == '') {
            $full_sql1 .= ' ORDER BY t_cache_first_time DESC,topic_id DESC';
        }
        if (($keywords != '') && (!$titleonly)) {
            $full_sql1 .= ' LIMIT ' . strval($max + $start);
        } else {
            if (db_uses_offset_syntax($GLOBALS['FORUM_DB']->connection_read)) {
                $full_sql1 .= ' LIMIT ' . strval($max) . ' OFFSET ' . strval($start);
            } else {
                $full_sql1 .= ' LIMIT ' . strval($start) . ',' . strval($max);
            }
        }

        if ($keywords != '') {
            $count_sql1 = '(SELECT COUNT(*) FROM (';
            $count_sql1 .= 'SELECT 1' . $sql1 . $where;
            $count_sql1 .= ' LIMIT 1000) counter)';
        } else {
            $count_sql1 = 'SELECT COUNT(*)' . $sql1 . $where;
        }

        if (($keywords != '') && (!$titleonly)) {
            $full_sql2 = 'SELECT ' . $select . $sql2 . $where;
            $full_sql2 .= ' ORDER BY t_cache_first_time DESC,topic_id DESC';
            $full_sql2 .= ' LIMIT ' . strval($max + $start);

            $full_sql = $full_sql1 . ' UNION ' . $full_sql2;

            if ($keywords != '') {
                $count_sql2 = '(SELECT COUNT(*) FROM (';
                $count_sql2 .= 'SELECT 1' . $sql2 . $where;
                $count_sql2 .= ' LIMIT 1000) counter)';
            } else {
                $count_sql1 = 'SELECT COUNT(*)' . $sql2 . $where;
            }

            $count_sql = 'SELECT (' . $count_sql1 . ') + (' . $count_sql2 . ') AS cnt';
        } else {
            $full_sql = $full_sql1;

            $count_sql = $count_sql1;
        }

        $topics = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query($full_sql, null, null, false, true);
        $total_topic_num = (get_allowed_forum_sql() == '') ? 0 : $GLOBALS['FORUM_DB']->query_value_if_there($count_sql);

        if (($keywords != '') && (!$titleonly)) {
            $topics = array_slice($topics, $start, $max); // We do it a weird way due to our UNION
        }

        return array($total_topic_num, $topics);
    }

    /**
     * Search posts.
     *
     * @param  string $keywords Keywords
     * @param  integer $start Start position
     * @param  integer $max Max position
     * @param  ?MEMBER $userid Member ID (null: no filter)
     * @param  ?string $searchuser Username (null: no filter)
     * @param  ?AUTO_LINK $forumid Forum ID (null: no filter)
     * @param  ?AUTO_LINK $topicid Topic ID (null: no filter)
     * @param  ?TIME $searchtime Time since (null: no filter)
     * @param  ?array $only_in Only in these forums (null: no filter)
     * @param  ?array $not_in Not in these forums (null: no filter)
     * @return array A pair: total topics, topics
     */
    public function search_posts($keywords, $start, $max, $userid = null, $searchuser = null, $forumid = null, $topicid = null, $searchtime = null, $only_in = null, $not_in = null)
    {
        cms_verify_parameters_phpdoc();

        require_code('database_search');
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $boolean_operator = 'AND';

        list($w) = build_content_where($keywords, false, $boolean_operator);
        if (multi_lang_content()) {
            $search_sql = preg_replace('#\?#', 'trans.text_original', $w);
            if ($search_sql == '') {
                $search_sql = '1=1';
            }
            $sql = 'FROM ' . $table_prefix . 'f_posts p';
            if (strpos(get_db_type(), 'mysql') !== false) {
                $sql .= (($search_sql == '1=1') ? '' : ' FORCE INDEX (p_post)');
            }
            $sql .= '
                JOIN ' . $table_prefix . 'translate trans ON trans.id=p.p_post
                JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id
                JOIN ' . $table_prefix . 'f_forums f ON t.t_forum_id=f.id
                WHERE ' . $search_sql;
        } else {
            $search_sql = preg_replace('#\?#', 'p_post', $w);
            if ($search_sql == '') {
                $search_sql = '1=1';
            }
            $sql = 'FROM ' . $table_prefix . 'f_posts p';
            if (strpos(get_db_type(), 'mysql') !== false) {
                $sql .= (($search_sql == '1=1') ? '' : ' FORCE INDEX (p_post)');
            }
            $sql .= '
                JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id
                JOIN ' . $table_prefix . 'f_forums f ON t.t_forum_id=f.id
                WHERE ' . $search_sql;
        }
        $sql .= ' AND p_cache_forum_id IN (' . get_allowed_forum_sql() . ')';
        if (addon_installed('unvalidated')) {
            $sql .= ' AND p_validated=1';
        }
        if (!has_privilege(get_member(), 'view_other_pt')) {
            $sql .= ' AND (p_intended_solely_for IS NULL OR p_intended_solely_for=' . strval(get_member()) . ' OR p_poster=' . strval(get_member()) . ')';
        }

        if (!is_null($userid)) {
            $sql .= ' AND p_poster=' . strval($userid);
        } elseif (!is_null($searchuser)) {
            $_userid = $GLOBALS['FORUM_DRIVER']->get_member_from_username($searchuser);
            if (is_null($_userid)) {
                warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($searchuser)));
            }
            $sql .= ' AND p_poster=' . strval($_userid);
        }

        if (!is_null($forumid)) {
            $sql .= ' AND p_cache_forum_id=' . strval($forumid);
        }

        if (!is_null($topicid)) {
            $sql .= ' AND p_topic_id=' . strval($topicid);
        }

        if (!is_null($searchtime)) {
            $sql .= ' AND p_time>' . strval(time() - $searchtime);
        }

        if (!is_null($only_in)) {
            if (count($only_in) == 0) {
                $sql .= ' AND 1=0';
            } else {
                $sql .= ' AND p_cache_forum_id IN (' . implode(',', array_map('strval', array_map('intval', $only_in))) . ')';
            }
        }

        if (!is_null($not_in)) {
            if (count($not_in) == 0) {
                $sql .= ' AND 1=1';
            } else {
                $sql .= ' AND p_cache_forum_id NOT IN (' . implode(',', array_map('strval', array_map('intval', $not_in))) . ')';
            }
        }

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(10);
        }

        $full_sql = 'SELECT *,t.id AS topic_id,p.id AS post_id,t.t_cache_first_title,f.id AS forum_id,f.f_name' . $sql;
        if ($keywords == '') {
            $full_sql .= ' ORDER BY p_time DESC,p.id DESC';
        }
        $posts = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query($full_sql, $max, $start);
        if ($keywords != '') {
            $count_sql = '(SELECT COUNT(*) FROM (';
            $count_sql .= 'SELECT 1' . $sql;
            $count_sql .= ' LIMIT 100) counter)';
        } else {
            $count_sql = 'SELECT COUNT(*)' . $sql;
        }
        $total_post_num = (get_allowed_forum_sql() == '') ? 0 : $GLOBALS['FORUM_DB']->query_value_if_there($count_sql);

        return array($total_post_num, $posts);
    }
}
