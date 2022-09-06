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
 * @param  integer $amount_gift_points The number of $total_points which should be credited as gift points (subtracts from gift points sent; only use to refund sent gift points)
 * @param  BINARY $anonymous Whether this transaction should be anonymous
 * @param  ?boolean $send_notifications Whether to send a notification to the member receiving the points (null: false, and staff should not receive a notifcation either)
 * @param  BINARY $locked Whether this transaction is irreversible (Ignored / always 1 when $amount_gift_points is > 0)
 * @param  ?array $code_explanation A Tuple explaining this ledger from a coding / API standpoint (it should follow the standard ['action', 'type', 'ID']) (null: there is no code explanation)
 * @return ?AUTO_LINK The ID of the point transaction (null: no transaction took place)
 */
function points_credit_member(int $member_id, string $reason, int $total_points, int $amount_gift_points = 0, int $anonymous = 0, ?bool $send_notifications = true, int $locked = 0, ?array $code_explanation = null) : ?int
{
    $id = non_overridden__points_credit_member($member_id, $reason, $total_points, $amount_gift_points, $anonymous, $send_notifications, $locked, $code_explanation);

    if (($id !== null) && (addon_installed('mentorr'))) {
        // Start add to mentor points if needed
        $mentor_id = $GLOBALS['SITE_DB']->query_select_value_if_there('members_mentors', 'mentor_id', ['member_id' => $member_id], ' AND date_and_time>' . strval(time() - (60 * 60 * 24 * 7)));

        if ((isset($mentor_id)) && ($mentor_id !== null) && (intval($mentor_id) != 0)) {
            // Credit points to mentor too
            non_overridden__points_credit_member($mentor_id, $reason, $total_points, $amount_gift_points, $anonymous, $send_notifications, $locked, json_encode(['reverse_link', $id]));
        }
    }

    return $id;
}
