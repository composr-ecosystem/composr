<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_notification_gift extends Hook_Notification
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
        return A_INSTANT_PT;
    }

    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority).
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes() : array
    {
        if (!addon_installed('giftr')) {
            return [];
        }

        $list = [];
        $list['gift'] = [do_lang('ACTIVITY'), do_lang('giftr:NOTIFICATION_TYPE_gift')];
        return $list;
    }
}
