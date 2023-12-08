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
class CMSSubscriptionRead
{
    /**
     * Get subscribed (monitored for notifications) forums.
     *
     * @return array List of forums
     */
    public function get_subscribed_forums()
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return array();
        }

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'notifications_enabled JOIN ' . $table_prefix . 'f_forums f ON l_code_category=' . db_function('CONCAT', array('\'forum:\'', 'f.id'));

        $select = array('f.id', 'f_name');

        $where = array('l_member_id' => $member_id, 'l_notification_code' => 'cns_topic');

        $extra = 'AND f.id IN (' . get_allowed_forum_sql() . ')';

        $rows = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra);

        $forums = array();
        foreach ($rows as $forum) {
            $forums[] = array(
                'forum_id' => $forum['id'],
                'forum_name' => $forum['f_name'],
                'icon_url' => '',
                'is_protected' => false,
                'new_post' => is_forum_unread($forum['id']),
            );
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
    public function get_subscribed_topics($start, $max)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return array(0, array());
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';

        $where = array('l_member_id' => $member_id, 'l_notification_code' => $notification_code);

        $_notifications = $GLOBALS['FORUM_DB']->query_select(
            'notifications_enabled',
            array('l_code_category'),
            $where,
            ' AND l_code_category NOT LIKE \'forum:%\' ORDER BY l_code_category ASC',
            $max,
            $start
        );

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
            $sql = 'FROM ' . $table_prefix . 'f_topics t JOIN ' . $table_prefix . 'f_posts p ON p.id=t.t_cache_first_post_id JOIN ' . $table_prefix . 'f_forums f ON t.t_forum_id=f.id ';
            $sql .= 'WHERE t.id IN (' . $notifications . ') AND f.id IN (' . get_allowed_forum_sql() . ') ';
            if (addon_installed('unvalidated')) {
                $sql .= 'AND t_validated=1 ';
            }
            $sql .= 'ORDER BY t_cache_first_time';
            $_topics = $GLOBALS['FORUM_DB']->query('SELECT *,t.id AS topic_id,p.id AS post_id,f.id AS forum_id ' . $sql);

            $total = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) ' . $sql);
        } else {
            $_topics = array();

            $total = 0;
        }

        $topics = array();
        foreach ($_topics as $topic) {
            $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_POST_KEY_NAME);
        }

        return array($total, $topics);
    }

    /**
     * Find all forums the current member is monitoring.
     *
     * @param  ?MEMBER $member_id Member ID (null: current member)
     * @return array List of forums
     */
    public function get_member_forum_monitoring($member_id = null)
    {
        if (is_null($member_id)) {
            $member_id = get_member();
        }

        $notification_code = 'cns_topic';

        $ret = array();
        $_x = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', array('l_code_category'), array('l_member_id' => $member_id, 'l_notification_code' => $notification_code), ' AND l_code_category LIKE \'forum:%\'');
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
    public function get_member_topic_monitoring($member_id = null)
    {
        if (is_null($member_id)) {
            $member_id = get_member();
        }

        $notification_code = 'cns_topic';

        $ret = array();
        $_x = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', array('l_code_category'), array('l_member_id' => $member_id, 'l_notification_code' => $notification_code), ' AND l_code_category NOT LIKE \'forum:%\' ORDER BY id DESC');
        foreach ($_x as $x) {
            $ret[] = mobiquo_val(intval($x['l_code_category']), 'int');
        }
        return $ret;
    }
}
