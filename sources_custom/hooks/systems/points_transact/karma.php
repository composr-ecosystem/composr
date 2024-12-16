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

class Hook_points_transact__karma
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
     */
    public function points_transact(?int $id, int $sending_member, int $receiving_member, string $reason, int $total_points, ?int $amount_gift_points, int $anonymous, ?bool $send_notifications, int $locked, string $t_type, string $t_subtype, string $t_type_id, ?int $time)
    {
        if (($id === null) || (!addon_installed('karma')) || (!addon_installed('points'))) {
            return;
        }

        if ($total_points <= 0) {
            return;
        }

        if ($time === null) {
            $time = time();
        }

        require_code('karma');
        require_code('karma2');
        require_code('points');

        // Received points from another member
        if (($t_type == 'points') && ($t_subtype == 'send')) {
            // No karma if Idolisr required and not installed or not specified in reason
            if (addon_installed('idolisr') && (get_option('karma_points_idolisr') == '1') && (strpos($reason, ': ') === false)) {
                return;
            }

            $influence = get_karmic_influence($sending_member);
            $ratio = floatval(get_option('karma_points'));

            if ($ratio > 0.0) { // Karma for every x points
                add_karma('good', $sending_member, $receiving_member, intval((floatval($total_points) / $ratio) * $influence), 'Received points from member', 'points_transaction', strval($id));
            } elseif ($ratio < 0.0) { // Influence * ratio karma regardless of points sent
                $ratio = abs($ratio);
                add_karma('good', $sending_member, $receiving_member, intval($influence * $ratio), 'Received points from member', 'points_transaction', strval($id));
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
        if ((!addon_installed('karma')) || (!addon_installed('points'))) {
            return;
        }

        // Undo karma when reversing point transactions
        if (($linked_ledger !== null) && ($status == LEDGER_STATUS_REVERSING)) {
            require_code('karma2');
            reverse_karma(null, null, null, 'points_transaction', strval($linked_ledger['id']));
        }
    }
}
