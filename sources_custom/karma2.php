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
