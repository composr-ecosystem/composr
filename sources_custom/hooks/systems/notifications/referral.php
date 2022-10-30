<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_notification_referral extends Hook_Notification
{
    /**
     * Find the initial setting that members have for a notification code (only applies to the member_could_potentially_enable members).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @return integer Initial setting
     */
    public function get_initial_setting(string $notification_code, ?string $category = null) : int
    {
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
        if (!addon_installed('referrals')) {
            return [];
        }

        $list = [];
        $list['referral'] = [do_lang('MEMBERS'), do_lang('referrals:NOTIFICATION_TYPE_referral')];
        return $list;
    }
}
