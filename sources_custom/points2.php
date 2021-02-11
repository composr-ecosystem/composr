<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

/**
 * Transfer gift-points into the specified member's account, courtesy of the system.
 *
 * @param  SHORT_TEXT $reason The reason for the transfer
 * @param  integer $amount The size of the transfer
 * @param  MEMBER $member_id The member the transfer is to
 * @param  boolean $include_in_log Whether to include a log line
 * @return ?AUTO_LINK ID of the gifts record if include_in_log was true (null: log was not created)
 */
function system_gift_transfer(string $reason, int $amount, int $member_id, bool $include_in_log = true) : ?int
{
    $id = non_overridden__system_gift_transfer($reason, $amount, $member_id, $include_in_log);

    if (addon_installed('mentorr')) {
        // Start add to mentor points if needed
        $mentor_id = $GLOBALS['SITE_DB']->query_select_value_if_there('members_mentors', 'mentor_id', ['member_id' => $member_id]);

        if ((isset($mentor_id)) && ($mentor_id !== null) && (intval($mentor_id) != 0)) {
            // Give points to mentor too
            $map = [
                'date_and_time' => time(),
                'amount' => $amount,
                'gift_from' => $GLOBALS['FORUM_DRIVER']->get_guest_id(),
                'gift_to' => $mentor_id,
                'anonymous' => 1,
            ];
            $map += insert_lang_comcode('reason', $reason, 4);
            $GLOBALS['SITE_DB']->query_insert('gifts', $map);
            $_before = point_info($mentor_id);
            $before = array_key_exists('points_gained_given', $_before) ? $_before['points_gained_given'] : 0;
            $new = strval($before + $amount);
            $GLOBALS['FORUM_DRIVER']->set_custom_field($mentor_id, 'points_gained_given', $new);

            global $TOTAL_POINTS_CACHE, $POINT_INFO_CACHE;
            if (array_key_exists($mentor_id, $TOTAL_POINTS_CACHE)) {
                $TOTAL_POINTS_CACHE[$mentor_id] += $amount;
            }
            if ((array_key_exists($mentor_id, $POINT_INFO_CACHE)) && (array_key_exists('points_gained_given', $POINT_INFO_CACHE[$mentor_id]))) {
                $POINT_INFO_CACHE[$mentor_id]['points_gained_given'] += $amount;
            }
        }
    }

    return $id;
}
