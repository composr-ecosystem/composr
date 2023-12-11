<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    group_points
 * @package    composr_homesite_support_credits
 */

/**
 * Get the total points in the specified member's account; some of these will probably have been spent already
 *
 * @param  MEMBER $member The member
 * @param  TIME $timestamp Time to get for (null: now)
 * @return integer The number of points the member has
 */
function total_points($member, $timestamp = null)
{
    global $TOTAL_POINTS_CACHE;

    if (is_null($timestamp)) {
        if (array_key_exists($member, $TOTAL_POINTS_CACHE)) {
            return $TOTAL_POINTS_CACHE[$member];
        }
    }

    $points = non_overridden__total_points($member, $timestamp);

    if ($GLOBALS['SITE_DB']->table_exists('credit_purchases')) {
        $credits = intval($GLOBALS['SITE_DB']->query_select_value('credit_purchases', 'SUM(num_credits)', array('member_id' => $member, 'purchase_validated' => 1)));

        if (!is_null($timestamp)) {
            $credits -= intval($GLOBALS['SITE_DB']->query_value_if_there('SELECT SUM(num_credits) FROM ' . get_table_prefix() . 'credit_purchases WHERE date_and_time>' . strval($timestamp) . ' AND member_id=' . strval($member)));
        }

        $points += $credits * 50;
    }

    if (is_null($timestamp)) {
        $TOTAL_POINTS_CACHE[$member] = $points;
    }

    return $points;
}

function get_group_points()
{
    $_group_points = $GLOBALS['SITE_DB']->query_select('group_points', array('*'), null, '', null, null, true);
    if (is_null($_group_points)) {
        $group_points = array();
    } else {
        $group_points = list_to_map('p_group_id', $_group_points);
    }
    return $group_points;
}
