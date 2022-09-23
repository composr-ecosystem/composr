<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 * Points hooks should follow this naming convention according to points_ledger column names: t_type__t_subtype (or just t_type to match all t_subtypes). By defining a hook accordingly, you are indicating that all transactions matching the t_type (and t_subtype if applicable) are considered 'low-impact'. This means they will be hidden from ledger tables on members' point profiles and instead displayed as a cumulative tally (determined by the points_profile function) at the top of their points profile.
 */
class Hook_points_support_credits__purchase
{
    /**
     * Calculate points earned to be displayed on POINTS_PROFILE.tpl.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  ?MEMBER $member_id_viewing The ID of the member who is doing the viewing (null: current member)
     * @return ?array Point record map containing label and data for use an aggregate tables. (null: addon disabled)
     */
    public function points_profile(int $member_id_of, ?int $member_id_viewing) : ?array
    {
        if (!addon_installed('points') || !addon_installed('composr_homesite_support_credits')) {
            return null;
        }

        require_code('points');
        $data = points_ledger_calculate(LEDGER_TYPE_RECEIVED | LEDGER_TYPE_SENT | LEDGER_TYPE_SPENT, $member_id_of, null, ' AND t_type=\'support_credits\' AND t_subtype=\'purchase\'');

        return [
            'label' => do_lang('customers:SPECIAL_CPF__cms_support_credits'),
            'data' => $data,
        ];
    }
}
