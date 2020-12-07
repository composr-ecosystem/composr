<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_points_support_credits
{
    /**
     * Get total points earned for support credits in the specified member's account; some of these will probably have been spent already.
     *
     * @param  MEMBER $member_id The ID of the member we are getting points for
     * @param  ?TIME $timestamp Time to get for (null: now)
     * @return integer the number of points the member has for support credits
     */
    public function total_points(int $member_id, ?int $timestamp) : int
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return 0;
        }

        $_credits = $GLOBALS['SITE_DB']->query_select_value('credit_purchases', 'SUM(num_credits)', ['member_id' => $member_id, 'purchase_validated' => 1]);
        $credits = @intval($_credits);

        if ($timestamp !== null) {
            $credits -= intval($GLOBALS['SITE_DB']->query_value_if_there('SELECT SUM(num_credits) FROM ' . get_table_prefix() . 'credit_purchases WHERE date_and_time>' . strval($timestamp) . ' AND member_id=' . strval($member_id)));
        }

        return $credits * 50;
    }

    /**
     * Calculate points earned to be displayed on POINTS_PROFILE.tpl.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  ?MEMBER $member_id_viewing The ID of the member who is doing the viewing (null: current member)
     * @param  array $point_info The map containing the members point info (fields as enumerated in description) from point_info()
     * @return ?array Point record map containing LABEL, COUNT, POINTS_EACH, and POINTS_TOTAL for use in POINTS_PROFILE.tpl. (null: addon disabled)
     */
    public function points_profile(int $member_id_of, ?int $member_id_viewing, array $point_info) : ?array
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return null;
        }

        $_points_gained_credits = $GLOBALS['SITE_DB']->query_select_value('credit_purchases', 'SUM(num_credits)', ['member_id' => $member_id_of, 'purchase_validated' => 1]);
        $points_gained_credits = @intval($_points_gained_credits);

        return [
            'LABEL' => do_lang('customers:SPECIAL_CPF__cms_support_credits'),
            'COUNT' => integer_format($points_gained_credits),
            'POINTS_EACH' => integer_format(50),
            'POINTS_TOTAL' => integer_format($points_gained_credits * 50)
        ];
    }
}
