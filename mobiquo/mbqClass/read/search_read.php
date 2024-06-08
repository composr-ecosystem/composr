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
    public function search_topics(string $keywords, int $start, int $max, ?int $userid = null, ?string $searchuser = null, ?int $forumid = null, ?bool $titleonly = false, ?int $searchtime = null, ?array $only_in = null, ?array $not_in = null) : array
    {
        cms_verify_parameters_phpdoc();

        require_code('database_search');
        $table_prefix = get_table_prefix();

        $sql1 = ' FROM ' . $table_prefix . 'f_posts p' . (($keywords == '') ? '' : $GLOBALS['FORUM_DB']->prefer_index('f_posts', 'p_title'));
        $sql1 .= ' JOIN ' . $table_prefix . 'f_topics t ON t.t_cache_first_post_id=p.id LEFT JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE 1=1';
        $sql2 = ' FROM ' . $table_prefix . 'f_topics t' . (($keywords == '') ? '' : $GLOBALS['FORUM_DB']->prefer_index('f_topics', 't_description'));
        $sql2 .= ' JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id LEFT JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE 1=1';

        $where = '';

        $where .= ' AND t_forum_id IN (' . get_allowed_forum_sql() . ')';

        if ($keywords != '') {
            list($w) = build_content_where($keywords);
            if ($w != '') {
                $sql1 .= ' AND ' . preg_replace('#\?#', 'p_title', $w);
                $sql2 .= ' AND ' . preg_replace('#\?#', 't_description', $w);
            }
        }

        if (addon_installed('validation')) {
            $where .= ' AND t_validated=1';
        }

        if ($userid !== null) {
            $where .= ' AND t_cache_first_member_id=' . strval($userid);
        } elseif ($searchuser !== null) {
            $_userid = $GLOBALS['FORUM_DRIVER']->get_member_from_username($searchuser);
            if ($_userid === null) {
                warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($searchuser)), false, false, 404);
            }
            $where .= ' AND t_cache_first_member_id=' . strval($_userid);
        }

        if ($forumid !== null) {
            $where .= ' AND t_forum_id=' . strval($forumid);
        }

        if ($searchtime !== null) {
            $where .= ' AND t_cache_last_time>' . strval(time() - $searchtime);
        }

        if ($only_in !== null) {
            if (empty($only_in)) {
                $where .= ' AND 1=0';
            } else {
                $where .= ' AND t_forum_id IN (' . implode(',', array_map('strval', array_map('intval', $only_in))) . ')';
            }
        }

        if ($not_in !== null) {
            if (empty($not_in)) {
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
            if ($GLOBALS['FORUM_DB']->driver->uses_offset_syntax()) {
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

        $topics = (get_allowed_forum_sql() == '') ? [] : $GLOBALS['FORUM_DB']->query($full_sql, null, 0, false, true);
        $total_topic_num = (get_allowed_forum_sql() == '') ? 0 : $GLOBALS['FORUM_DB']->query_value_if_there($count_sql);

        if (($keywords != '') && (!$titleonly)) {
            $topics = array_slice($topics, $start, $max); // We do it a weird way due to our UNION
        }

        return [$total_topic_num, $topics];
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
    public function search_posts(string $keywords, int $start, int $max, ?int $userid = null, ?string $searchuser = null, ?int $forumid = null, ?int $topicid = null, ?int $searchtime = null, ?array $only_in = null, ?array $not_in = null) : array
    {
        cms_verify_parameters_phpdoc();

        require_code('database_search');
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        list($w) = build_content_where($keywords);
        if ($w == '') {
            $search_sql = '1=1';
        } else {
            $search_sql = preg_replace('#\?#', $GLOBALS['FORUM_DB']->translate_field_ref('p_post'), $w);
        }
        $sql = 'FROM ' . $table_prefix . 'f_posts p' . (($search_sql == '1=1') ? '' : $GLOBALS['FORUM_DB']->prefer_index('f_posts', 'p_post'));
        $sql .= ' JOIN ' . $table_prefix . 'f_topics t ON p.p_topic_id=t.id JOIN ' . $table_prefix . 'f_forums f ON t.t_forum_id=f.id';
        $sql .= ' WHERE ' . $search_sql;
        $sql .= ' AND p_cache_forum_id IN (' . get_allowed_forum_sql() . ')';
        if (addon_installed('validation')) {
            $sql .= ' AND p_validated=1';
        }
        if (!has_privilege(get_member(), 'view_other_pt')) {
            $sql .= ' AND (p_whisper_to_member IS NULL OR p_whisper_to_member=' . strval(get_member()) . ' OR p_posting_member=' . strval(get_member()) . ')';
        }

        if ($userid !== null) {
            $sql .= ' AND p_posting_member=' . strval($userid);
        } elseif ($searchuser !== null) {
            $_userid = $GLOBALS['FORUM_DRIVER']->get_member_from_username($searchuser);
            if ($_userid === null) {
                warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($searchuser)), false, false, 404);
            }
            $sql .= ' AND p_posting_member=' . strval($_userid);
        }

        if ($forumid !== null) {
            $sql .= ' AND p_cache_forum_id=' . strval($forumid);
        }

        if ($topicid !== null) {
            $sql .= ' AND p_topic_id=' . strval($topicid);
        }

        if ($searchtime !== null) {
            $sql .= ' AND p_time>' . strval(time() - $searchtime);
        }

        if ($only_in !== null) {
            if (empty($only_in)) {
                $sql .= ' AND 1=0';
            } else {
                $sql .= ' AND p_cache_forum_id IN (' . implode(',', array_map('strval', array_map('intval', $only_in))) . ')';
            }
        }

        if ($not_in !== null) {
            if (empty($not_in)) {
                $sql .= ' AND 1=1';
            } else {
                $sql .= ' AND p_cache_forum_id NOT IN (' . implode(',', array_map('strval', array_map('intval', $not_in))) . ')';
            }
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        $full_sql = 'SELECT *,t.id AS topic_id,p.id AS post_id,t.t_cache_first_title,f.id AS forum_id,f.f_name' . $sql;
        if ($keywords == '') {
            $full_sql .= ' ORDER BY p_time DESC,p.id DESC';
        }
        $posts = (get_allowed_forum_sql() == '') ? [] : $GLOBALS['FORUM_DB']->query($full_sql, $max, $start, false, false, ['p_post' => 'LONG_TRANS__COMCODE']);
        if ($keywords != '') {
            $count_sql = '(SELECT COUNT(*) FROM (';
            $count_sql .= 'SELECT 1' . $sql;
            $count_sql .= ' LIMIT 100) counter)';
        } else {
            $count_sql = 'SELECT COUNT(*)' . $sql;
        }
        $total_post_num = (get_allowed_forum_sql() == '') ? 0 : $GLOBALS['FORUM_DB']->query_value_if_there($count_sql, false, false, ['p_post' => 'LONG_TRANS__COMCODE']);

        return [$total_post_num, $posts];
    }
}
