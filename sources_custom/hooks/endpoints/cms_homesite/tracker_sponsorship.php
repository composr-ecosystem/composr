<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_tracker_sponsorship
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => ['keep_session'],
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
        }

        $bug_id = get_param_integer('bug_id', 0);
        $amount = get_param_integer('amount', 0);

        $reason = 'Sponsored issue #' . strval($bug_id);
        $agreement = 'This escrow shall be considered satisfied when [url="tracker issue #' . strval($bug_id) . '"]' . get_base_url() . '/tracker/view.php?id=' . strval($bug_id) . '[/url] has been resolved. The resolving member will receive the points escrowed. Should the issue be closed / not implemented, the escrow shall be considered cancelled and all points refunded.';

        require_code('points_escrow');
        require_code('points2');

        // Create the escrow for the sponsorship
        if ($type == 'add') {
            $id = escrow_points(get_member(), null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
            if ($id !== null) {
                return ['success' => true, 'id' => $id];
            }
            return ['success' => false, 'error_details' => 'Could not create an escrow for this sponsorship. Perhaps you do not have enough points (you have ' . integer_format(points_balance(get_member())) . ')?'];
        }

        // Editing a sponsorship requires cancelling the previous escrow and making a new one
        if ($type == 'edit') {
            $id = cancel_escrow($id, get_member(), 'Sponsorship amended');
            if ($id === null) {
                return ['success' => false, 'error_details' => 'Could not cancel the escrow / refund the points of the original sponsorship.'];
            }
            $id = escrow_points(get_member(), null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
            if ($id !== null) {
                return ['success' => true, 'id' => $id];
            }
            return ['success' => false, 'error_details' => 'We cancelled the old sponsorship and refunded the points, but could not create a new sponsorship. Perhaps you do not have enough points (you have ' . integer_format(points_balance(get_member())) . ')?'];
        }

        if ($type == 'delete') {
            $id = cancel_escrow($id, get_member(), 'Sponsorship cancelled');
            if ($id === null) {
                return ['success' => false, 'error_details' => 'Could not cancel the escrow / refund the points of the sponsorship.'];
            }
            return ['success' => true];
        }

        if ($type == 'delete-all') { // $id is the bug ID
            $reason = get_param_string('reason');
            $ids = cancel_all_escrows_by_content('tracker_issue', $id, $reason);
            foreach ($ids as $id => $value) {
                if ($value === null) {
                    return ['success' => false, 'error_details' => 'Could not cancel all the escrows / refund the points of the sponsorships.'];
                }
            }
            return ['success' => true];
        }

        if ($type == 'complete-all') { // $id is the bug ID
            $recipient = get_param_integer('recipient');
            $reporter = get_param_integer('reporter');

            if (($recipient < 1) || (is_guest($recipient))) {
                return ['success' => false, 'error_details' => 'A recipient / issue handler must be assigned to complete the sponsorships.'];
            }

            $ids = complete_all_escrows_by_content($recipient, 'tracker_issue', $id);
            foreach ($ids as $id => $value) {
                if ($value[0] === null) {
                    return ['success' => false, 'error_details' => 'Could not mark all escrows completed and award the points.'];
                }
            }

            // FUDGE: Now credit 25 baseline points to both the reporter and the handler (undo previous transactions for this issue)
            points_transactions_reverse_all(true, null, null, 'tracker_issue', '', strval($id));
            points_credit_member($recipient, 'Resolved tracker issue #' . strval($id), 25, 0, true, 0, 'tracker_issue', 'resolve', strval($id));
            if (($reporter > 0) && !is_guest($reporter)) {
                points_credit_member($reporter, 'Reported resolved tracker issue #' . strval($id), 25, 0, true, 0, 'tracker_issue', 'report_resolved', strval($id));
            }

            return ['success' => true];
        }

        if ($type == 'reopen-all') { // $id is the bug ID
            // When re-opening issues, reverse the baseline points awarded
            points_transactions_reverse_all(true, null, null, 'tracker_issue', '', strval($id));
            return ['success' => true];
        }

        return ['success' => false, 'error_details' => 'Invalid Type'];
    }
}
