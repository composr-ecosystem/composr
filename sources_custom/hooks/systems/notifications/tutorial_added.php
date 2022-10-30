<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_notification_tutorial_added extends Hook_Notification
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
        return A_NA;
    }

    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority)
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes() : array
    {
        if (!addon_installed('composr_tutorials')) {
            return [];
        }

        $list = [];
        $list['tutorial_added'] = [do_lang('CONTENT'), 'New external tutorial added'];
        return $list;
    }

    /**
     * Get a list of members who have enabled this notification (i.e. have permission to AND have chosen to or are defaulted to).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @param  ?array $to_member_ids List of member IDs we are restricting to (null: no restriction). This effectively works as a intersection set operator against those who have enabled.
     * @param  ?integer $from_member_id The member ID doing the sending. Either a MEMBER or a negative number (e.g. A_FROM_SYSTEM_UNPRIVILEGED) (null: current member)
     * @param  integer $start Start position (for pagination)
     * @param  integer $max Maximum (for pagination)
     * @return array A pair: Map of members to their notification setting, and whether there may be more
     */
    public function list_members_who_have_enabled(string $notification_code, ?string $category = null, ?array $to_member_ids = null, ?int $from_member_id = null, int $start = 0, int $max = 300) : array
    {
        $members = $this->_all_members_who_have_enabled($notification_code, $category, $to_member_ids, $start, $max);
        $members = $this->_all_members_who_have_enabled_with_page_access($members, 'tutorials', $notification_code, $category, $to_member_ids, $start, $max);

        return $members;
    }
}
