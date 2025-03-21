<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
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
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('cms_homesite_tracker')) {
            return null;
        }
        if (!addon_installed('points')) {
            return null;
        }

        return ['authorization' => false];

        /* TODO: get this working
        return [
            'authorization' => 'member',
        ];
        */
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
        $bug_id = get_param_integer('bug_id', 0);
        $amount = get_param_integer('amount', 0);

        require_code('points_escrow__sponsorship');
        require_code('mantis');

        if ($type == 'add') {
            $escrow_id = escrow_create_sponsorship($bug_id, $amount);
            if ($escrow_id !== null) {
                return ['success' => true, 'id' => $escrow_id];
            }
            return ['success' => false, 'error_details' => 'Could not create an escrow for this sponsorship. Perhaps you do not have enough points (you have ' . integer_format(points_balance(get_member())) . ')?'];
        }

        if ($type == 'edit') {
            $escrow_id = escrow_edit_sponsorship($id, $bug_id, $amount);
            if ($escrow_id !== null) {
                return ['success' => true, 'id' => $escrow_id];
            }
            return ['success' => false, 'error_details' => 'We could not amend your sponsorship. Perhaps you do not have enough points (you have ' . integer_format(points_balance(get_member())) . ')? Be aware it is possible we still cancelled your old sponsorship.'];
        }

        if ($type == 'delete') {
            $ledger_id = escrow_cancel_sponsorship($id);
            if ($ledger_id === null) {
                return ['success' => false, 'error_details' => 'Could not cancel the escrow / refund the points of the sponsorship.'];
            }
            return ['success' => true];
        }

        if ($type == 'delete-all') { // $id is the bug ID
            $reason = get_param_string('reason');
            $results = escrow_cancel_all_sponsorships($id, $reason);
            if ($results[1] === false) {
                return ['success' => false, 'error_details' => 'Could not cancel all the escrows / refund the points of the sponsorships. Note that it is possible some were cancelled, but not all.'];
            }
            return ['success' => true];
        }

        if ($type == 'complete-all') { // $id is the bug ID
            $recipient = get_param_integer('recipient');
            $reporter = get_param_integer('reporter');

            if (($recipient < 1) || (is_guest($recipient))) {
                return ['success' => false, 'error_details' => 'A recipient / issue handler must be assigned to complete the sponsorships.'];
            }

            $results = escrow_complete_all_sponsorships($id, $recipient);
            if ($results[1] === false) {
                return ['success' => false, 'error_details' => 'Could not mark all escrows completed and award the points. Note that some may have been marked completed, but not all. Also note that basic tracker points have not been awarded due to this error.'];
            }

            $tracker_points = award_tracker_points($id, $recipient, $reporter);
            if ($tracker_points === false) {
                return ['success' => false, 'error_details' => 'Could not award points for resolved tracker issue. Note that sponsorships have been awarded if there were any.'];
            }

            return ['success' => true];
        }

        if ($type == 'reopen-all') { // $id is the bug ID
            // When re-opening issues, reverse the baseline points awarded
            reverse_tracker_points($id);
            return ['success' => true];
        }

        return ['success' => false, 'error_details' => 'Invalid Type'];
    }
}
