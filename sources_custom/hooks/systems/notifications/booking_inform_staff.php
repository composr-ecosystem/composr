<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

/**
 * Hook class.
 */
class Hook_notification_booking_inform_staff extends Hook_notification__Staff
{
    /**
     * Find the initial setting that members have for a notification code (only applies to the member_could_potentially_enable members).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @param  MEMBER $member_id The member the notification would be for
     * @return integer Initial setting
     */
    public function get_initial_setting(string $notification_code, ?string $category, int $member_id) : int
    {
        if (!$this->is_first_admin($member_id)) {
            return A_NA;
        }

        return A__STATISTICAL;
    }

    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority).
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes() : array
    {
        if (!addon_installed('booking')) {
            return [];
        }

        $list = [];
        $list['booking_inform_staff'] = [do_lang('booking:BOOKINGS'), do_lang('NOTIFICATION_TYPE_booking_inform_staff')];
        return $list;
    }
}
