<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Find whether a particular member is online.
 *
 * @param  MEMBER $member_id Member ID
 * @return boolean Whether they are
 */
function is_member_online($member_id)
{
    if (is_guest($member_id)) {
        return false;
    }

    static $cache = array();
    if (isset($cache[$member_id])) {
        return $cache[$member_id];
    }

    $users_online_time_seconds = intval(get_option('users_online_time')) * 60;
    $sql = 'SELECT last_activity FROM ' . get_table_prefix() . 'sessions WHERE last_activity>' . strval(time() - $users_online_time_seconds) . ' AND member_id=' . strval($member_id);
    $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
    $ret = !is_null($result);

    $cache[$member_id] = $ret;

    return $ret;
}

/**
 * Check friendship from this member.
 *
 * @param  MEMBER $user_id Member checking from
 * @return boolean Whether exists
 */
function i_follow_u($user_id)
{
    $logged_in_user_id = get_member();
    if ($logged_in_user_id == $user_id) {
        return false;
    }

    if (!addon_installed('chat')) {
        return false;
    }

    $result = $GLOBALS['FORUM_DB']->query_select_value_if_there('chat_friends', 'member_likes', array('member_likes' => $logged_in_user_id, 'member_liked' => $user_id));
    return !is_null($result);
}

/**
 * Check friendship to this member.
 *
 * @param  MEMBER $user_id Member checking to
 * @return boolean Whether exists
 */
function u_follow_me($user_id)
{
    $logged_in_user_id = get_member();
    if ($logged_in_user_id == $user_id) {
        return false;
    }

    if (!addon_installed('chat')) {
        return false;
    }

    $result = $GLOBALS['FORUM_DB']->query_select_value_if_there('chat_friends', 'member_liked', array('member_liked' => $logged_in_user_id, 'member_likes' => $user_id));
    return !is_null($result);
}

