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

define('TAPATALK_MESSAGE_BOX_INBOX', 1);
define('TAPATALK_MESSAGE_BOX_SENT', 2);

/**
 * Get number of unread private topics in a particular "box type".
 * This is not a normal Composr view, but Tapatalk is designed like this.
 *
 * @param  ?integer $box_type Message box type, a TAPATALK_MESSAGE_BOX_* constant (null: don't care)
 * @return integer Number of topics
 */
function get_num_unread_private_topics(?int $box_type = null) : int
{
    $member_id = get_member();

    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics t';
    $sql .= ' LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_read_logs r ON t.id=r.l_topic_id AND l_member_id=' . strval($member_id);
    $sql .= ' WHERE';
    $sql .= ' t.t_forum_id IS NULL';
    if (addon_installed('validation')) {
        $sql .= ' AND t_validated=1';
    }
    if ($box_type === TAPATALK_MESSAGE_BOX_INBOX) {
        $sql .= ' AND (t_pt_to_member=' . strval($member_id) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval($member_id) . '))';
    } elseif ($box_type === TAPATALK_MESSAGE_BOX_SENT) {
        $sql .= ' AND t_pt_from_member=' . strval($member_id);
    } else {
        $sql .= ' AND (t_pt_from_member=' . strval($member_id) . ' OR t_pt_to_member=' . strval($member_id) . ' OR EXISTS(SELECT * FROM ' . $table_prefix . 'f_special_pt_access WHERE s_topic_id=t.id AND s_member_id=' . strval($member_id) . '))';
    }
    $sql .= ' AND (t_pt_from_member<>' . strval(get_member()) . ' OR ' . db_string_not_equal_to('t_pt_from_category', do_lang('TRASH')) . ')';
    $sql .= ' AND (t_pt_to_member<>' . strval(get_member()) . ' OR ' . db_string_not_equal_to('t_pt_to_category', do_lang('TRASH')) . ')';
    $sql .= ' AND (l_time IS NULL OR l_time<t_cache_last_time)'; // Cannot get join match OR gets one and it is behind of last post
    $sql .= ' AND t_cache_last_time>' . strval(time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))); // Within tracking range

    return $GLOBALS['FORUM_DB']->query_value_if_there($sql);
}
