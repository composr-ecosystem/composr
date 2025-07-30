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
 * @param  ?MEMBER $creating_member The member creating the escrow (null: current member)
 * @return ?AUTO_LINK The escrow / sponsorship ID (null: error)
 */
function escrow_create_sponsorship(int $bug_id, int $amount, ?int $creating_member = null) : ?int
{
    if ($creating_member === null) {
        $creating_member = get_member();
    }

    $reason = 'Sponsored issue #' . strval($bug_id);
    $agreement = 'This escrow shall be considered satisfied when [url="tracker issue #' . strval($bug_id) . '"]' . get_base_url() . '/tracker/view.php?id=' . strval($bug_id) . '[/url] has been resolved. The resolving member will receive the points escrowed. Should the issue be closed / not implemented, the escrow shall be considered cancelled and all points refunded.';

    return escrow_points($creating_member, null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
}

/**
 * Amend an escrow sponsorship.
 * This cancels the previous escrow and creates a new one.
 *
 * @param  AUTO_LINK $bug_id The issue ID
 * @param  AUTO_LINK $user_id The user ID from the tracker who created the sponsorship
 * @param  integer $new_amount The new amount for the sponsorship
 * @param  ?MEMBER $editing_member The member editing the escrow (null: current member)
 * @return ?AUTO_LINK The escrow / sponsorship ID (null: error)
 */
function escrow_edit_sponsorship(int $bug_id, int $user_id, int $new_amount, ?int $editing_member = null) : ?int
{
    if ($editing_member === null) {
        $editing_member = get_member();
    }

    // If amount is 0 or less, we are actually cancelling.
    if ($new_amount <= 0) {
        escrow_cancel_sponsorship($bug_id, $user_id, $editing_member);
        return null;
    }

    $rows = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['sending_member' => $user_id, 'content_type' => 'tracker_issue', 'content_id' => strval($bug_id)]);
    foreach ($rows as $row) {
        if ($row['status'] < 2) {
            continue;
        }

        $id = cancel_escrow($row['id'], get_member(), 'Sponsorship amended', $row);
        if ($id === null) {
            return null;
        }
    }

    return escrow_create_sponsorship($bug_id, $new_amount, $editing_member);
}

/**
 * Cancel an escrow sponsorship.
 *
 * @param  AUTO_LINK $bug_id The issue ID
 * @param  AUTO_LINK $user_id The user ID from the tracker who created the sponsorship
 * @param  ?MEMBER $cancelling_member The member cancelling the escrow (null: current member)
 * @return ?AUTO_LINK The points ledger record which was reversed (null: error)
 */
function escrow_cancel_sponsorship(int $bug_id, int $user_id, ?int $cancelling_member = null) : ?int
{
    if ($cancelling_member === null) {
        $cancelling_member = get_member();
    }

    $rows = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['sending_member' => $user_id, 'content_type' => 'tracker_issue', 'content_id' => strval($bug_id)]);
    foreach ($rows as $row) {
        if ($row['status'] < 2) {
            continue;
        }

        return cancel_escrow($row['id'], $cancelling_member, 'Sponsorship cancelled', $row);
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
    $ids = cancel_all_escrows_by_content('tracker_issue', strval($bug_id), $reason);
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
 * @param  ?MEMBER $recipient The member receiving all the points (null: the handler user)
 * @return array Duple (Output of complete_all_escrows_by_content, whether the operation was considered successful)
 */
function escrow_complete_all_sponsorships(int $bug_id, ?int $recipient = null) : array
{
    if ($recipient === null) {
        $rows = $GLOBALS['SITE_DB']->query_parameterised('SELECT handler_id FROM mantis_bug_table WHERE id={id}', ['id' => $bug_id]);
        if (array_key_exists(0, $rows) && ($rows[0]['handler_id'] != 0)) {
            $recipient = $rows[0]['handler_id'];
        } else {
            return [[], false];
        }
    }

    if (($recipient < 1) || (is_guest($recipient))) { // Cannot resolve escrows to guest
        return [[], false];
    }

    $ids = complete_all_escrows_by_content($recipient, 'tracker_issue', strval($bug_id));
    $success = true;
    foreach ($ids as $id => $value) {
        if ($value[0] === null) {
            $success = false;
            break;
        }
    }

    if ($success) {
        // Update sponsorship status in Mantis
        $GLOBALS['SITE_DB']->query_parameterised('UPDATE mantis_sponsorship_table SET paid={paid} WHERE bug_id={bug_id}', [
            'paid' => 3,
            'bug_id' => $bug_id,
        ]);
    }

    return [$ids, $success];
}
