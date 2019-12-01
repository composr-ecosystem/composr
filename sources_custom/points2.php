<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

if (!function_exists('system_gift_transfer')) {
    /**
     * Transfer gift-points into the specified member's account, courtesy of the system.
     *
     * @param  SHORT_TEXT $reason The reason for the transfer
     * @param  integer $amount The size of the transfer
     * @param  MEMBER $member_id The member the transfer is to
     */
    function system_gift_transfer($reason, $amount, $member_id)
    {
        require_lang('points');
        require_code('points');

        if (is_guest($member_id)) {
            return;
        }
        if ($amount == 0) {
            return;
        }

        $map = [
            'date_and_time' => time(),
            'amount' => $amount,
            'gift_from' => $GLOBALS['FORUM_DRIVER']->get_guest_id(),
            'gift_to' => $member_id,
            'anonymous' => 1,
        ];
        $map += insert_lang_comcode('reason', $reason, 4);
        $GLOBALS['SITE_DB']->query_insert('gifts', $map);
        $_before = point_info($member_id);
        $before = array_key_exists('points_gained_given', $_before) ? $_before['points_gained_given'] : 0;
        $new = strval($before + $amount);
        $GLOBALS['FORUM_DRIVER']->set_custom_field($member_id, 'points_gained_given', $new);

        global $TOTAL_POINTS_CACHE, $POINT_INFO_CACHE;
        if (array_key_exists($member_id, $TOTAL_POINTS_CACHE)) {
            $TOTAL_POINTS_CACHE[$member_id] += $amount;
        }
        if ((array_key_exists($member_id, $POINT_INFO_CACHE)) && (array_key_exists('points_gained_given', $POINT_INFO_CACHE[$member_id]))) {
            $POINT_INFO_CACHE[$member_id]['points_gained_given'] += $amount;
        }

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

                if (array_key_exists($mentor_id, $TOTAL_POINTS_CACHE)) {
                    $TOTAL_POINTS_CACHE[$mentor_id] += $amount;
                }
                if ((array_key_exists($mentor_id, $POINT_INFO_CACHE)) && (array_key_exists('points_gained_given', $POINT_INFO_CACHE[$mentor_id]))) {
                    $POINT_INFO_CACHE[$mentor_id]['points_gained_given'] += $amount;
                }
            }
        }

        if (get_forum_type() == 'cns') {
            require_code('cns_posts_action');
            require_code('cns_posts_action2');
            cns_member_handle_promotion($member_id);
        }
    }
}
