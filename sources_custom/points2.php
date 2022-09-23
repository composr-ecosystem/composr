<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

/**
 * Credit or refund points to a member from the system.
 *
 * @param  MEMBER $member_id The member to credit
 * @param  SHORT_TEXT $reason The reason for this credit in the logs
 * @param  integer $total_points The total points to credit (including gift points when applicable)
 * @param  integer $amount_gift_points The number of $total_points which should be credited as gift points (if > 0, this transaction becomes a refund)
 * @param  BINARY $anonymous Whether this transaction should be anonymous
 * @param  ?AUTO_LINK $linked_to The ID of the points ledger transaction being refunded by this (ignored unless $amount_gift_points > 0) (null: this refund is not related to any past ledger)
 * @param  ?boolean $send_notifications Whether to send notifications for this transaction (false: only the staff get it) (true: both the member and staff get it) (null: neither the member nor staff get it)
 * @param  BINARY $locked Whether this transaction is irreversible (Ignored / always 1 when $amount_gift_points is > 0)
 * @param  ID_TEXT $t_type An identifier to relate this transaction with other transactions of the same $type (e.g. content type)
 * @param  ID_TEXT $t_subtype An identifier to relate this transaction with other transactions of the same $type and $subtype (e.g. an action performed on the $type)
 * @param  ID_TEXT $t_type_id Some content or row ID of the specified $type
 * @param  ?TIME $time The time this transaction occurred (null: now)
 * @return ?AUTO_LINK The ID of the point transaction (null: no transaction took place)
 */
function points_credit_member(int $member_id, string $reason, int $total_points, int $amount_gift_points = 0, int $anonymous = 0, ?int $linked_to = null, ?bool $send_notifications = true, int $locked = 0, string $t_type = '', string $t_subtype = '', string $t_type_id = '', ?int $time = null) : ?int
{
    if ($time === null) {
        $time = time();
    }

    $id = non_overridden__points_credit_member($member_id, $reason, $total_points, $amount_gift_points, $anonymous, $linked_to, $send_notifications, $locked, $t_type, $t_subtype, $t_type_id, $time);

    // If a transaction was created and this was not a refund (gift points = 0), award points to the mentor if applicable
    if (($id !== null) && ($amount_gift_points == 0) && (addon_installed('mentorr'))) {
        // Start add to mentor points if needed
        $mentor_id = $GLOBALS['SITE_DB']->query_select_value_if_there('members_mentors', 'mentor_id', ['member_id' => $member_id], ' AND date_and_time>' . strval($time - (60 * 60 * 24 * 7)));

        if ((isset($mentor_id)) && ($mentor_id !== null) && (intval($mentor_id) != 0)) {
            // Credit points to mentor too, but link it to the original transaction with reverse_link; this allows the transaction to reverse itself if the original one gets reversed.
            non_overridden__points_credit_member($mentor_id, $reason, $total_points, $amount_gift_points, $anonymous, $id, $send_notifications, $locked, 'reverse_link', '', strval($id), $time);
        }
    }

    return $id;
}
