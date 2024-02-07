<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class CMSSubscriptionRead
{
    /**
     * Get subscribed (monitored for notifications) forums.
     *
     * @return array List of forums
     */
    public function get_subscribed_forums() : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return [];
        }

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'notifications_enabled JOIN ' . $table_prefix . 'f_forums f ON l_code_category=' . db_function('CONCAT', ['\'forum:\'', 'f.id']);

        $select = ['f.id', 'f_name'];

        $where = ['l_member_id' => $member_id, 'l_notification_code' => 'cns_topic'];

        $extra = 'AND f.id IN (' . get_allowed_forum_sql() . ')';

        $rows = (get_allowed_forum_sql() == '') ? [] : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra);

        $forums = [];
        foreach ($rows as $forum) {
            $forums[] = [
                'forum_id' => $forum['id'],
                'forum_name' => $forum['f_name'],
                'icon_url' => '',
                'is_protected' => false,
                'new_post' => is_forum_unread($forum['id']),
            ];
        }
        return $forums;
    }

    /**
     * Get subscribed (monitored for notifications) topics.
     *
     * @param  integer $start Start position
     * @param  integer $max Total results
     * @return array A pair: total topics, topics
     */
    public function get_subscribed_topics(int $start, int $max) : array
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return [0, []];
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';

        $where = ['l_member_id' => $member_id, 'l_notification_code' => $notification_code];

        $_notifications = $GLOBALS['FORUM_DB']->query_select(
            'notifications_enabled',
            ['l_code_category'],
            $where,
            ' AND l_code_category NOT LIKE \'forum:%\' ORDER BY l_code_category ASC',
            $max,
            $start
        );

        $topics = [];
        $total = 0;
        if (!empty($_notifications)) {
            $notifications = '';
            foreach ($_notifications as $notification) {
                if ($notifications != '') {
                    $notifications .= ',';
                }
                if (is_numeric($notification['l_code_category'])) {
                    $notifications .= $notification['l_code_category'];
                }
            }
            $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
            $sql = ' FROM ' . $table_prefix . 'f_topics t JOIN ' . $table_prefix . 'f_posts p ON p.id=t.t_cache_first_post_id JOIN ' . $table_prefix . 'f_forums f ON t.t_forum_id=f.id';
            $sql .= ' WHERE t.id IN (' . $notifications . ') AND f.id IN (' . get_allowed_forum_sql() . ') ';
            if (addon_installed('validation')) {
                $sql .= 'AND t_validated=1 ';
            }
            $sql .= 'ORDER BY t_cache_first_time';
            $total = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*)' . $sql);

            $max = 25;
            $start = 0;

            do {
                $_topics = $GLOBALS['FORUM_DB']->query('SELECT *,t.id AS topic_id,p.id AS post_id,f.id AS forum_id' . $sql, $max, $start);

                foreach ($_topics as $topic) {
                    $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_POST_KEY_NAME);
                }

                $start += $max;
            } while (!empty($_topics));
        }

        return [$total, $topics];
    }

    /**
     * Find all forums the current member is monitoring.
     *
     * @param  ?MEMBER $member_id Member ID (null: current member)
     * @return array List of forums
     */
    public function get_member_forum_monitoring(?int $member_id = null) : array
    {
        if ($member_id === null) {
            $member_id = get_member();
        }

        $notification_code = 'cns_topic';

        $ret = [];
        $_x = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', ['l_code_category'], ['l_member_id' => $member_id, 'l_notification_code' => $notification_code], ' AND l_code_category LIKE \'forum:%\'');
        foreach ($_x as $x) {
            $ret[] = mobiquo_val(intval(substr($x['l_code_category'], 6)), 'int');
        }
        return $ret;
    }

    /**
     * Find all topics the current member is monitoring.
     *
     * @param  ?MEMBER $member_id Member ID (null: current member)
     * @return array List of topics
     */
    public function get_member_topic_monitoring(?int $member_id = null) : array
    {
        if ($member_id === null) {
            $member_id = get_member();
        }

        $notification_code = 'cns_topic';

        $ret = [];
        $_x = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', ['l_code_category', 'id'], ['l_member_id' => $member_id, 'l_notification_code' => $notification_code], ' AND l_code_category NOT LIKE \'forum:%\' ORDER BY id DESC');
        foreach ($_x as $x) {
            $ret[] = mobiquo_val(intval($x['l_code_category']), 'int');
        }
        return $ret;
    }
}
