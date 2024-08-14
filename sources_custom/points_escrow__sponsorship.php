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

function init__points_escrow__sponsorship()
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('cms_homesite')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
    }
    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }

    require_code('points_escrow');
    require_code('points2');
}

/**
 * Create an escrow as a sponsorship for an issue on the tracker.
 *
 * @param  AUTO_LINK $bug_id The issue ID
 * @param  integer $amount The amount to escrow
 * @return ?AUTO_LINK The escrow / sponsorship ID (null: error)
 */
function escrow_create_sponsorship(int $bug_id, int $amount) : ?int
{
    $reason = 'Sponsored issue #' . strval($bug_id);
    $agreement = 'This escrow shall be considered satisfied when [url="tracker issue #' . strval($bug_id) . '"]' . get_base_url() . '/tracker/view.php?id=' . strval($bug_id) . '[/url] has been resolved. The resolving member will receive the points escrowed. Should the issue be closed / not implemented, the escrow shall be considered cancelled and all points refunded.';

    return escrow_points(get_member(), null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
}

/**
 * Amend an escrow sponsorship.
 * This cancels the previous escrow and creates a new one.
 *
 * @param  AUTO_LINK $escrow_id The ID of the escrow to cancel
 * @param  AUTO_LINK $bug_id The issue ID
 * @param  integer $new_amount The new amount for the sponsorship
 * @return ?AUTO_LINK The escrow / sponsorship ID (null: error)
 */
function escrow_edit_sponsorship(int $escrow_id, int $bug_id, int $new_amount) : ?int
{
    // If amount is 0 or less, we are actually cancelling.
    if ($new_amount <= 0) {
        escrow_cancel_sponsorship($escrow_id);
        return null;
    }

    $row = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['id' => $escrow_id]);
    if (array_key_exists(0, $row) && ($row[0]['status'] >= 2)) {
        $id = cancel_escrow($escrow_id, get_member(), 'Sponsorship amended', $row[0]);
        if ($id === null) {
            return null;
        }
    }

    return escrow_create_sponsorship($bug_id, $new_amount);
}

/**
 * Cancel an escrow sponsorship.
 *
 * @param  AUTO_LINK $escrow_id The escrow / sponsorship ID to cancel
 * @return ?AUTO_LINK The points ledger record which was reversed (null: error)
 */
function escrow_cancel_sponsorship(int $escrow_id) : ?int
{
    $row = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['id' => $escrow_id]);
    if (array_key_exists(0, $row) && ($row[0]['status'] >= 2)) {
        return cancel_escrow($escrow_id, get_member(), 'Sponsorship cancelled', $row[0]);
    }

    return null;
}

/**
 * Cancel all escrows / sponsorships on a given issue.
 *
 * @param  AUTO_LINK $bug_id The tracker issue on which to cancel all sponsorships
 * @param  LONG_TEXT $reason The reason for cancellation
 * @return array Duple (Output of cancel_all_escrows_by_content, whether the operation was considered successful)
 */
function escrow_cancel_all_sponsorships(int $bug_id, string $reason) : array
{
    $ids = cancel_all_escrows_by_content('tracker_issue', $bug_id, $reason);
    $success = true;
    foreach ($ids as $id => $value) {
        if ($value === null) {
            $success = false;
            break;
        }
    }

    return [$ids, $success];
}

/**
 * Complete all escrows / sponsorships tied to an issue, and credit a recipient with the points.
 *
 * @param  AUTO_LINK $bug_id The issue on which to complete all sponsorships
 * @param  MEMBER $recipient The member receiving all the points
 * @return array Duple (Output of complete_all_escrows_by_content, whether the operation was considered successful)
 */
function escrow_complete_all_sponsorships(int $bug_id, int $recipient) : array
{
    if (($recipient < 1) || (is_guest($recipient))) { // Cannot resolve escrows to guest
        return [[], false];
    }

    $ids = complete_all_escrows_by_content($recipient, 'tracker_issue', $bug_id);
    $success = true;
    foreach ($ids as $id => $value) {
        if ($value[0] === null) {
            $success = false;
            break;
        }
    }

    return [$ids, $success];
}
