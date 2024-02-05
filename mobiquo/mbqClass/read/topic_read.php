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
class CMSTopicRead
{
    protected const GET_TOPICS_ALL = 0;
    protected const GET_TOPICS_UNREAD_ONLY = 1;
    protected const GET_TOPICS_PARTICIPATED_ONLY = 2;

    /**
     * Get basic details of some topics.
     *
     * @param  array $topic_ids List of topic IDs
     * @return array Details
     */
    public function get_topic_statuses(array $topic_ids) : array
    {
        cms_verify_parameters_phpdoc();

        $member_id = get_member();

        if (empty($topic_ids)) {
            return [];
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $sql_ids = '';
        foreach ($topic_ids as $topic_id) {
            if ($sql_ids != '') {
                $sql_ids .= ',';
            }
            $sql_ids .= strval($topic_id);
        }
        $sql = 'SELECT *,id AS topic_id FROM ' . $table_prefix . 'f_topics WHERE id IN (' . $sql_ids . ')';
        $sql .= ' AND t_forum_id IN (' . get_allowed_forum_sql() . ')';
        if (addon_installed('validation')) {
            $sql .= ' AND t_validated=1';
        }
        $_topics = ($sql_ids == '') ? [] : $GLOBALS['FORUM_DB']->query($sql);

        $topics = [];
        foreach ($_topics as $topic) {
            $topics[] = [
                'topic_id' => $topic['topic_id'],
                'is_subscribed' => get_topic_subscription_status($topic['topic_id'], $member_id),
                'can_subscribe' => !is_guest($member_id),
                'is_closed' => ($topic['t_is_open'] == 0),
                'last_reply_time' => $topic['t_cache_last_time'],
                'new_post' => is_topic_unread($topic['topic_id'], $member_id, $topic),
                'reply_number' => $topic['t_cache_num_posts'],
                'view_number' => $topic['t_num_views'],
            ];
        }
        return $topics;
    }

    /**
     * Get some topics from a forum.
     *
     * @param  string $mode Mode
     * @set TOP ANN ALL
     * @param  AUTO_LINK $forum_id Forum ID
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array A tuple of details: total topics, topics, forum name, unread sticky count, unread announce count, action assessment
     */
    public function get_topics(string $mode, int $forum_id, int $start, int $max) : array
    {
        if (!has_category_access(get_member(), 'forums', strval(($forum_id == 0) ? db_get_first_id() : $forum_id))) {
            access_denied('I_ERROR');
        }

        $where_basic = ['t_forum_id' => $forum_id];
        if (addon_installed('validation')) {
            $where_basic['t_validated'] = 1;
        }
        $where = $where_basic;

        switch ($mode) {
            case 'TOP': // Sticky topics
                $where['t_pinned'] = 1;
                $where['t_cascading'] = 0;
                break;

            case 'ANN': // Announcement topics
                $where['t_cascading'] = 1;
                break;

            default: // Standard topics
                $where['t_pinned'] = 0;
                $where['t_cascading'] = 0;
                break;
        }

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $_details = $GLOBALS['FORUM_DB']->query_select(
            'f_topics t JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id',
            ['*', 'f.id as forum_id', 't.id AS topic_id', 'p.id AS post_id'],
            $where,
            'ORDER BY t_cache_last_time DESC',
            $max,
            $start
        );

        if (!empty($_details)) {
            $forum_details = $_details[0];
        } else {
            $_forum_details = $GLOBALS['FORUM_DB']->query_select('f_forums f', ['*', 'f.id AS forum_id'], ['f.id' => $forum_id], '', 1);
            if (!isset($_forum_details[0])) {
                // Ideally we'd do a normal MISSING_RESOURCE, but tapatalk is sending some spurious requests to a forum '0' after doing moderation actions on iOS
                return [
                    0,
                    [],
                    do_lang('MISSING_RESOURCE', 'forum'),
                    0,
                    0,
                    [
                        'can_post' => false,
                        'can_upload' => false,
                    ],
                ];
            }
            $forum_details = $_forum_details[0];
        }
        $forum_name = $forum_details['f_name'];

        $total_topic_num = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', $where, ' AND t_cache_first_member_id IS NOT NULL');

        $extra = ' AND (SELECT l_time FROM ' . $table_prefix . 'f_read_logs l WHERE l_topic_id=t.id AND l_member_id=' . strval(get_member()) . ')<t.t_cache_last_time';
        $unread_sticky_count = $GLOBALS['FORUM_DB']->query_select_value('f_topics t', 'COUNT(*)', ['t_pinned' => 1, 't_cascading' => 0] + $where_basic, $extra);
        $unread_announce_count = $GLOBALS['FORUM_DB']->query_select_value('f_topics t', 'COUNT(*)', ['t_cascading' => 1] + $where_basic, $extra);

        $action_details = action_assessment_forum($forum_details, get_member());

        return [
            $total_topic_num,
            $_details,
            $forum_name,
            $unread_sticky_count,
            $unread_announce_count,
            $action_details,
        ];
    }

    /**
     * Get some topics matching some filters.
     *
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @param  array $filters List of filter maps (supports only_in, not_in, excuded_topics)
     * @param  integer $method Method (a GET_TOPICS_* constant)
     * @param  ?mixed $method_data Data for search method (null: N/A)
     * @return array A pair: total topics, topics
     */
    public function get_topics_advanced(int $start, int $max, array $filters, int $method = 0, $method_data = null) : array
    {
        cms_verify_parameters_phpdoc();

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $conditions = [];

        // Implement filter
        if (!empty($filters)) {
            if (isset($filters['only_in'])) {
                $or_list = '';
                foreach ($filters['only_in'] as $_forum_id) {
                    if ($or_list != '') {
                        $or_list .= ' OR ';
                    }
                    $or_list .= strval($_forum_id);
                }
                if ($or_list != '') {
                    $conditions[] = 't_forum_id IN (' . $or_list . ')';
                } else {
                    $conditions[] = '0=1';
                }
            }

            if (isset($filters['not_in'])) {
                $or_list = '';
                foreach ($filters['not_in'] as $_forum_id) {
                    if ($or_list != '') {
                        $or_list .= ' OR ';
                    }
                    $or_list .= strval($_forum_id);
                }
                if ($or_list != '') {
                    $conditions[] = 't_forum_id NOT IN (' . $or_list . ')';
                }
            }

            if (isset($filters['excluded_topics'])) {
                $or_list = '';
                foreach ($filters['excluded_topics'] as $_forum_id) {
                    if ($or_list != '') {
                        $or_list .= ' OR ';
                    }
                    $or_list .= strval($_forum_id);
                }
                if ($or_list != '') {
                    $conditions[] = 'id NOT IN (' . $or_list . ')';
                }
            }
        }

        // Enforce permissions
        if (get_allowed_forum_sql() != '') {
            $conditions[] = 't_forum_id IN (' . get_allowed_forum_sql() . ')';
        } else {
            // No PTs at least
            $conditions[] = 't_forum_id IS NOT NULL';
        }

        $conditions_full = []; // For performance we won't put the less important ones that aren't easily indexable into a count query

        // No empty topic shells
        $conditions_full[] = 't_cache_first_member_id IS NOT NULL';

        // Validated-only
        if (addon_installed('validation')) {
            $conditions_full[] = 't_validated=1';
        }

        // Only unread ones?
        if ($method == self::GET_TOPICS_UNREAD_ONLY) {
            if (is_guest()) {
                return [0, []];
            }

            $subquery = 'SELECT l_topic_id FROM ' . $table_prefix . 'f_read_logs WHERE ';
            $subquery .= 'l_member_id=' . strval($member_id) . ' AND ';
            $subquery .= 'l_topic_id=t.id AND ';
            $subquery .= 'l_time>=t_cache_last_time';

            $conditions[] = 't_cache_last_time>' . strval(time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))) . ' AND NOT EXISTS(' . $subquery . ')';
        }

        // Only participated ones?
        if ($method == self::GET_TOPICS_PARTICIPATED_ONLY) {
            if (is_guest()) {
                return [0, []];
            }

            $participant_id = $method_data;

            $conditions[] = 'pp.p_poster=' . strval($participant_id);

            // Run query
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $sql = $table_prefix . 'f_posts pp JOIN ' . $table_prefix . 'f_topics t ON t.id=pp.p_topic_id JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE ' . implode(' AND ', $conditions);
            $total_forum_topics = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $sql . ' GROUP BY t.id');
            $sql .= implode(' AND ', $conditions_full);
            $sql .= ' GROUP BY t.id ORDER BY t_cache_last_time DESC,t.id DESC';
            $forum_topics = $GLOBALS['FORUM_DB']->query('SELECT *,t.id AS topic_id,p.id AS post_id,f.id AS forum_id FROM ' . $sql, $max, $start);

            return [$total_forum_topics, $forum_topics];
        }

        // Run query
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $sql = $table_prefix . 'f_topics t WHERE ' . implode(' AND ', $conditions);
        $total_forum_topics = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $sql);
        $sql = $table_prefix . 'f_topics t JOIN ' . $table_prefix . 'f_posts p ON t.t_cache_first_post_id=p.id JOIN ' . $table_prefix . 'f_forums f ON f.id=t.t_forum_id WHERE ' . implode(' AND ', $conditions);
        $sql .= ' AND ' . implode(' AND ', $conditions_full);
        $sql .= ' ORDER BY t_cache_last_time DESC,t.id DESC';
        $forum_topics = $GLOBALS['FORUM_DB']->query('SELECT *,t.id AS topic_id,p.id AS post_id,f.id AS forum_id FROM ' . $sql, $max, $start);

        return [$total_forum_topics, $forum_topics];
    }
}
