<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
 * @param  MEMBER $member_id_of Who we are looking at logs for
 * @param  MEMBER $member_id_viewing Who we are looking at logs using the account of
 * @param  integer $max Maximum number of records to return
 * @param  integer $start The starting record number
 * @param  SHORT_TEXT $sortable Fields by which we want to order (blank: no ordering)
 * @param  ID_TEXT $sort_order Direction of ordering to use
 * @set ASC DESC
 * @param  ?integer $reversed Filter by the given reversed status (null: do not filter)
 * @param  ?TIME $after_time Only return transactions after this time (null: do not filter by time)
 * @return array Duple of total rows in the database and an array of rows
 */
function karma_get_logs(string $type, int $member_id_of, int $member_id_viewing, int $max, int $start = 0, string $sortable = '', string $sort_order = 'DESC', ?int $reversed = null, ?int $after_time = null) : array
{
    $where = [];
    $end = '';
    
    switch ($type) {
        case 'sender': // Logs where the member sent karma to another member (karmic influence)
            $where = ['k_member_from' => $member_id_of];
            
            $end = ' AND k_member_to<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
            break;
        case 'recipient': // Logs where the member received karma (either from other members or the system)
            $where = ['k_member_to' => $member_id_of];
            $end = '';
            break;
        case 'sender_recipient': // Logs where the member sent karma to other members or received karma (either from other members or the system)
            $end = ' AND ((k_member_from=' . strval($member_id_of) . ' AND k_member_to<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
            $end .= ') OR (k_member_to=' . strval($member_id_of) . '))'; // We also want to include karma from the system
            break;
        case 'credit': // Logs where the member received karma from the system
            $where = ['k_member_to' => $member_id_of, 'k_member_from' => $GLOBALS['FORUM_DRIVER']->get_guest_id()];
            $end = '';
            break;
        case 'all': // All logs involving the member
            $end = ' AND (k_member_to=' . strval($member_id_of) . ' OR k_member_from=' . strval($member_id_of) . ')';
            break;
    }
    
    if ($reversed !== null) {
        $where['k_reversed'] = $reversed;
    }
    
    if ($after_time !== null) {
        $end .= ' AND k_date_and_time>=' . strval($after_time);
    }
    
    $max_rows = $GLOBALS['SITE_DB']->query_select_value('karma', 'COUNT(*)', $where, $end);
    
    if ($sortable != '') {
        $end .= ' ORDER BY ' . $sortable . ' ' . $sort_order;
    }
    $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], $where, $end, $max, $start);
    
    return [$max_rows, $rows];
}

/**
 * Add karma to a member via some content.
 * This adds the entry into the karma logs so it can later be undone with reverse_karma.
 *
 * @param  ID_TEXT $type Type of karma
 * @set good bad
 * @param  ?MEMBER $member_from The member who applied this karma (null: system / guest)
 * @param  MEMBER $member_to The member whose karma to adjust
 * @param  integer $amount The amount by which to adjust the karma (negative numbers are allowed)
 * @param  SHORT_TEXT $reason The reason "phrase" for this karma adjustment
 * @param  ID_TEXT $content_type The content type associated with this karma
 * @param  ID_TEXT $content_id The content ID associated with this karma
 */
function add_karma(string $type, ?int $member_from, int $member_to, int $amount, string $reason, string $content_type, string $content_id)
{
    if (($type != 'good') && ($type != 'bad')) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    // Nothing to do if karma is 0 or member we are applying karma on is a guest
    if (($amount == 0) || is_guest($member_to)) {
        return;
    }

    if ($member_from === null) {
        $member_from = $GLOBALS['FORUM_DRIVER']->get_guest_id();
    }

    // Add the record into the database
    $map = [
        'k_type' => $type,
        'k_member_from' => $member_from,
        'k_member_to' => $member_to,
        'k_amount' => $amount,
        'k_content_type' => $content_type,
        'k_content_id' => $content_id,
        'k_date_and_time' => time(),
        'k_reversed' => 0
    ];
    $map += insert_lang_comcode('k_reason', $reason, 4);
    $GLOBALS['SITE_DB']->query_insert('karma', $map);

    // Actualise the karma
    _adjust_karma($type, $member_to, $amount);
}

/**
 * Reverses karma that was assigned to a member via some content.
 * You must at least specify either $id, $member_from, $member_to, or both $content_type and $content_id.
 *
 * @param  ?AUTO_LINK $id The ID of the karma log to be reversed (null: do not filter by this)
 * @param  ?MEMBER $member_from The member who applied this karma to be reversed (null: do not filter by this)
 * @param  ?MEMBER $member_to The member on which the karma to be reversed was adjusted (null: do not filter by this)
 * @param  ?SHORT_TEXT $reason The reason "phrase" that was used in add_karma (null: do not filter by this)
 * @param  ?ID_TEXT $content_type The content type associated with the karma to be reversed (null: do not filter by this)
 * @param  ?ID_TEXT $content_id The content ID associated with the karma to be reversed (null: do not filter by this)
 */
function reverse_karma(?int $id = null, ?int $member_from = null, ?int $member_to = null, ?string $content_type = null, ?string $content_id = null)
{
    if (($id === null) && ($member_from === null) && ($member_to === null) && (($content_type === null) || ($content_id === null))) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }
    
    require_lang('karma');

    // Build our search query string
    $query_string = ['k_reversed' => 0];
    if ($id !== null) {
        $query_string['id'] = $id;
    }
    if ($member_from !== null) {
        $query_string['k_member_from'] = $member_from;
    }
    if ($member_to !== null) {
        $query_string['k_member_to'] = $member_to;
    }
    if ($content_type !== null) {
        $query_string['k_content_type'] = $content_type;
    }
    if ($content_id !== null) {
        $query_string['k_content_id'] = $content_id;
    }
    $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], $query_string);

    // Actualise reversal of karma, and log each
    foreach ($rows as $row) {
        _adjust_karma($row['k_type'], intval($row['k_member_to']), -intval($row['k_amount']));
        log_it('REVERSED_KARMA', $row['id']);
    }

    // Mark all relevant logs as reversed so they cannot be reversed again
    $GLOBALS['SITE_DB']->query_update('karma', ['k_reversed' => 1], $query_string);
}

/**
 * Directly modify the amount of karma a member has on their special custom profile fields.
 * This bypasses the karma logs.
 *
 * @param  ID_TEXT $type Type of karma to adjust
 * @set good bad
 * @param  MEMBER $member_id The member whose karma to adjust
 * @param  integer $amount The amount by which to adjust the karma (negative numbers are allowed)
 */
function _adjust_karma(string $type, int $member_id, int $amount)
{
    if (($type != 'good') && ($type != 'bad')) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    if (($amount == 0) || is_guest($member_id)) {
        return;
    }

    $field_name = $type . '_karma';

    $values = $GLOBALS['FORUM_DRIVER']->get_custom_fields($member_id);
    if ($values === null) {
        $values = [];
    }
    $old = array_key_exists($field_name, $values) ? @intval($values[$field_name]) : 0;
    $new = max(-2147483648, min(2147483647, $old + $amount)); // TODO: #3046 in tracker
    $GLOBALS['FORUM_DRIVER']->set_custom_field($member_id, $field_name, strval($new));
}
