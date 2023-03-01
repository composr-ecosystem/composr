<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    banner_click_points
 */

/**
 * Hook class.
 * Points hooks should follow this naming convention according to points_ledger column names: t_type__t_subtype (or just t_type to match all t_subtypes). By defining a hook accordingly, you are indicating that all transactions matching the t_type (and t_subtype if applicable) are considered 'low-impact'. This means they will be hidden from ledger tables on members' point profiles and instead displayed as a cumulative tally (determined by the points_profile function) at the top of their points profile.
 */
class Hook_points_banner__click
{
    /**
     * Determine the aggregate row language for POINTS_PROFILE.tpl.
     *
     * @param  ?MEMBER $member_id_of The ID of the member who is being viewed (null: was run from the admin ledger)
     * @param  ?MEMBER $member_id_viewing The ID of the member who is doing the viewing (null: current member)
     * @return array List containing label for use with aggregate point tables
     */
    public function points_profile(?int $member_id_of, ?int $member_id_viewing) : array
    {
        return [
            'label' => 'Banner clicks', // FUDGE
        ];
    }
}
