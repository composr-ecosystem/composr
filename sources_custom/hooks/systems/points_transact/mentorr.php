<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    mentorr
 */

class Hook_points_transact__mentorr
{
    /**
     * Run post point transaction tasks.
     *
     * @param  ?AUTO_LINK $id The ID of the transaction that was created (null: No transaction was created)
     * @param  MEMBER $sending_member The ID of the member sending the points
     * @param  MEMBER $receiving_member The ID of the member receiving the points
     * @param  SHORT_TEXT $reason The reason for this transaction in the logs
     * @param  integer $total_points The total number of points to transact (includes gift points when applicable)
     * @param  ?integer $amount_gift_points The strict number of $total_points which should come from the sender's gift points balance (null: use as many gift points the sender has available)
     * @param  BINARY $anonymous Whether the sender should be hidden from those without the privilege to trace anonymous transactions
     * @param  ?boolean $send_notifications Whether to send notifications for this transaction (false: only the staff get it) (true: both the member and staff get it) (null: neither the member nor staff get it)
     * @param  BINARY $locked Whether this transaction is irreversible
     * @param  ID_TEXT $t_type An identifier to relate this transaction with other transactions of the same $type (e.g. content type)
     * @param  ID_TEXT $t_subtype An identifier to relate this transaction with other transactions of the same $type and $subtype (e.g. an action performed on the $type)
     * @param  ID_TEXT $t_type_id Some content or row ID of the specified $type
     * @param  ?TIME $time The time this transaction occurred (null: now)
     * @param  boolean $force Whether the transaction was forced even if the sender did not have enough points
     */
    public function points_transact(?int $id, int $sending_member, int $receiving_member, string $reason, int $total_points, ?int $amount_gift_points, int $anonymous, ?bool $send_notifications, int $locked, string $t_type, string $t_subtype, string $t_type_id, ?int $time, bool $force)
    {
        if ((!addon_installed('mentorr')) || (!addon_installed('points'))) {
            return;
        }

        if ($time === null) {
            $time = time();
        }

        // Add mentorr points via another credit transaction where applicable if this was a system credit
        if (($id !== null) && ($sending_member == $GLOBALS['FORUM_DRIVER']->get_guest_id())) {
            $mentor_member_id = $GLOBALS['SITE_DB']->query_select_value_if_there('members_mentors', 'mentor_member_id', ['member_id' => $receiving_member], ' AND date_and_time>' . strval($time - (60 * 60 * 24 * 7)));

            if ($mentor_member_id !== null && $mentor_member_id != $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
                points_credit_member($mentor_member_id, $reason, $total_points, $anonymous, true, 0, 'mentorr', 'transact', strval($id), $time);
            }
        }
    }

    /**
     * Run post point refund tasks.
     *
     * @param  ?AUTO_LINK $id The ID of the transaction that was created (null: No transaction was created)
     * @param  MEMBER $sending_member The ID of the member refunding the points (e.g. the receiving_member in the original transaction)
     * @param  MEMBER $receiving_member The ID of the member receiving the refunded points (e.g. the sending_member in the original transaction)
     * @param  SHORT_TEXT $reason The reason for this refund in the logs
     * @param  integer $total_points The total number of points to refund (includes gift points when applicable)
     * @param  integer $amount_gift_points The number of $total_points which should be refunded as gift points (subtracted from gift_points_sent)
     * @param  BINARY $anonymous Whether the sender should be hidden from those without the privilege to trace anonymous transactions
     * @param  ?array $linked_ledger The database row of the points_ledger transaction being refunded by this (null: this refund is not related to any past ledger)
     * @param  ?boolean $send_notifications Whether to send notifications for this transaction (false: only the staff get it) (true: both the member and staff get it) (null: neither the member nor staff get it)
     * @param  ID_TEXT $t_type An identifier to relate this transaction with other transactions of the same $type (e.g. content type)
     * @param  ID_TEXT $t_subtype An identifier to relate this transaction with other transactions of the same $type and $subtype (e.g. an action performed on the $type)
     * @param  ID_TEXT $t_type_id Some content or row ID of the specified $type
     * @param  ?TIME $time The time this transaction occurred (null: now)
     * @param  integer $status The status to use for the record (see LEDGER_STATUS_*)
     */
    public function points_refund(?int $id, int $sending_member, int $receiving_member, string $reason, int $total_points, int $amount_gift_points, int $anonymous, ?array $linked_ledger, ?bool $send_notifications, string $t_type, string $t_subtype, string $t_type_id, ?int $time, int $status)
    {
        if ((!addon_installed('mentorr')) || (!addon_installed('points'))) {
            return;
        }

        // If we are reversing a transaction, we should check if the transaction being reversed has a mentorr transaction tied to it, and if so, reverse that as well.
        if (($linked_ledger !== null) && ($status == LEDGER_STATUS_REVERSING)) {
            points_transactions_reverse_all(true, null, null, 'mentorr', 'transact', strval($linked_ledger['id']));
        }
    }
}
