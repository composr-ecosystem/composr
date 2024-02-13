<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Get the karma of the specified member.
 *
 * @param  ?MEMBER $member_id The member to retrieve karma (null: current member)
 * @return array Duple of integers: Good karma, bad karma
 */
function get_karma(?int $member_id = null) : array
{
    if (!addon_installed('karma') || (get_forum_type() != 'cns')) {
        return [0, 0];
    }

    if ($member_id === null) {
        $member_id = get_member();
    }

    $good_karma = 0;
    $bad_karma = 0;

    $values = $GLOBALS['FORUM_DRIVER']->get_custom_fields($member_id);
    if ($values === null) {
        $values = [];
    }
    if (array_key_exists('good_karma', $values)) {
        $good_karma = intval(($values['good_karma'] != '') ? $values['good_karma'] : 0);
    }
    if (array_key_exists('bad_karma', $values)) {
        $bad_karma = intval(($values['bad_karma'] != '') ? $values['bad_karma'] : 0);
    }

    return [$good_karma, $bad_karma];
}

/**
 * Get the karmic influence of a given member.
 *
 * @param  ?MEMBER $member_id The member to look up (null: current member)
 * @return float The karmic influence of the member
 */
function get_karmic_influence(?int $member_id = null) : float
{
    if (!addon_installed('karma') || (get_forum_type() != 'cns')) {
        return 0.0;
    }

    if ($member_id === null) {
        $member_id = get_member();
    }

    // No privilege for karmic influence? No karmic influence!
    if (!has_privilege($member_id, 'has_karmic_influence')) {
        return 0.0;
    }

    $karmic_influence = 1.0; // Start with 1 influence

    // Add additional karmic influence if we have the privilege
    if (has_privilege($member_id, 'has_additional_karmic_influence')) {
        $karmic_influence += floatval(get_option('karma_influence_additional'));
    }

    // Subtract influence for warnings
    if (addon_installed('cns_warnings')) {
        $days_to_search = intval(get_option('karma_influence_warnings'));
        $active_warnings = $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'COUNT(*)', ['w_member_id' => $member_id, 'w_is_warning' => 1], ' AND w_time>' . strval(time() - (60 * 60 * 24 * $days_to_search)));

        $karmic_influence -= floatval($active_warnings) * floatval(get_option('karma_influence_warnings_amount'));
    }

    // If using voting power and our necessary addons are installed, use that for influence and return.
    if ((get_option('karma_influence_use_voting_power') == '1') && addon_installed('points')) {
        require_code('cns_polls_action2');
        require_code('points');
        $karmic_influence += cns_points_to_voting_power(points_balance($member_id));
        $karmic_influence = $karmic_influence * floatval(get_option('karma_influence_multiplier'));
        return ($karmic_influence < 0.0) ? 0.0 : $karmic_influence;
    }

    // Account age
    if (floatval(get_option('karma_influence_account_age')) != 0.0) {
        $karmic_influence += (floatval(time() - $GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_join_time')) / (60.0 * 60.0 * 24.0)) / floatval(get_option('karma_influence_account_age'));
    }

    // Forum posts
    if (floatval(get_option('karma_influence_forum_posts')) != 0.0) {
        $karmic_influence += floatval($GLOBALS['FORUM_DRIVER']->get_post_count($member_id)) / floatval(get_option('karma_influence_forum_posts'));
    }

    // Karma
    if (floatval(get_option('karma_influence_karma')) != 0.0) {
        $karma = get_karma($member_id);
        $karmic_influence += (floatval($karma[0]) - floatval($karma[1])) / floatval(get_option('karma_influence_karma'));
    }

    if (addon_installed('points')) {
        require_code('points');

        // Lifetime points
        if (floatval(get_option('karma_influence_lifetime_points')) != 0.0) {
            $karmic_influence += floatval(points_lifetime($member_id)) / floatval(get_option('karma_influence_lifetime_points'));
        }

        // Points balance
        if (floatval(get_option('karma_influence_points')) != 0.0) {
            $karmic_influence += floatval(points_balance($member_id)) / floatval(get_option('karma_influence_points'));
        }
    }

    // Influence multiplier
    $karmic_influence = $karmic_influence * floatval(get_option('karma_influence_multiplier'));

    return ($karmic_influence < 0.0) ? 0.0 : $karmic_influence;
}

/**
 * Get the karma logs a member has had.
 *
 * @param  ID_TEXT $type The type of logs we are looking for
 * @set sender recipient sender_recipient credit all
 * @param  ?MEMBER $member_id_filter Filter logs by this member ID (null: do not filter)
 * @param  ?MEMBER $member_id_viewing Who is viewing the logs (null: current member)
 * @param  integer $max Maximum number of records to return
 * @param  integer $start The starting record number
 * @param  SHORT_TEXT $sortable Fields by which we want to order (blank: no ordering)
 * @param  ID_TEXT $sort_order Direction of ordering to use
 * @set ASC DESC
 * @param  ?integer $reversed Filter by the given reversed status (null: do not filter)
 * @param  ?TIME $from_time Only return transactions on or after this time (null: do not filter by this)
 * @param  ?TIME $to_time Only return transactions before this time (null: do not filter by this)
 * @return array Duple of total rows in the database and an array of rows
 */
function karma_get_logs(string $type, ?int $member_id_filter = null, ?int $member_id_viewing = null, int $max = 50, int $start = 0, string $sortable = '', string $sort_order = 'DESC', ?int $reversed = null, ?int $from_time = null, ?int $to_time = null) : array
{
    $where = [];
    $end = '';

    if ($member_id_viewing === null) {
        $member_id_viewing = get_member();
    }

    switch ($type) {
        case 'sender': // Only return logs where karma was sent to another member
            if ($member_id_filter !== null) {
                $where = ['k_member_from' => $member_id_filter];
            }
            $end = ' AND k_member_to<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
            break;
        case 'recipient': // Logs where the member received karma
            if ($member_id_filter !== null) {
                $where = ['k_member_to' => $member_id_filter];
            }
            $end = ' AND k_member_from<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
            $end = '';
            break;
        case 'sender_recipient': // Logs where the member sent karma to other members or received karma (either from other members or the system)
            if ($member_id_filter !== null) {
                $end = ' AND ((k_member_from=' . strval($member_id_filter) . ' AND k_member_to<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ')';
                $end .= ' OR (k_member_to=' . strval($member_id_filter) . '))';
            } else {
                $end = ' AND k_member_to<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
            }
            break;
        case 'credit': // Logs where the member received karma from the system
            if ($member_id_filter !== null) {
                $where = ['k_member_to' => $member_id_filter, 'k_member_from' => $GLOBALS['FORUM_DRIVER']->get_guest_id()];
            } else {
                $where = ['k_member_from' => $GLOBALS['FORUM_DRIVER']->get_guest_id()];
            }
            $end = '';
            break;
        case 'all': // All logs
            if ($member_id_filter !== null) {
                $end = ' AND (k_member_to=' . strval($member_id_filter) . ' OR k_member_from=' . strval($member_id_filter) . ')';
            }
            break;
    }

    if ($reversed !== null) {
        $where['k_reversed'] = $reversed;
    }

    if ($from_time !== null) {
        $end .= ' AND k_date_and_time>=' . strval($from_time);
    }

    if ($to_time !== null) {
        $end .= ' AND k_date_and_time<' . strval($to_time);
    }

    $max_rows = $GLOBALS['SITE_DB']->query_select_value('karma', 'COUNT(*)', $where, $end);

    if ($sortable != '') {
        $end .= ' ORDER BY ' . $sortable . ' ' . $sort_order;
    }
    $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], $where, $end, $max, $start);

    return [$max_rows, $rows];
}
