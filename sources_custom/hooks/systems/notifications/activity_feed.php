<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_notification_activity_feed extends Hook_Notification
{
    /**
     * Find whether a handled notification code supports categories.
     * (Content types, for example, will define notifications on specific categories, not just in general. The categories are interpreted by the hook and may be complex. E.g. it might be like a regexp match, or like FORUM:3 or TOPIC:100).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @return boolean Whether it does
     */
    public function supports_categories(string $notification_code) : bool
    {
        return true;
    }

    /**
     * Standard function to create the standardised category tree.
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?ID_TEXT $id The ID of where we're looking under (null: N/A)
     * @return array Tree structure
     */
    public function create_category_tree(string $notification_code, ?string $id) : array
    {
        if (!addon_installed('activity_feed')) {
            return [];
        }

        $page_links = [];

        $notification_category = get_param_string('id', null);
        $done_in_url = ($notification_category === null);

        $types = addon_installed('chat') ? $GLOBALS['SITE_DB']->query_select('chat_friends', ['member_liked'], ['member_likes' => get_member()]) : []; // Only show options for friends to simplify
        $types2 = $GLOBALS['SITE_DB']->query_select('notifications_enabled', ['l_code_category'], ['l_notification_code' => substr($notification_code, 0, 80), 'l_member_id' => get_member()]); // Already monitoring members who may not be friends
        foreach ($types2 as $type) {
            $types[] = ['member_liked' => intval($type['l_code_category'])];
        }
        foreach ($types as $type) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($type['member_liked'], false, USERNAME_DEFAULT_NULL);

            if ($username !== null) {
                $page_links[$type['member_liked']] = [
                    'id' => strval($type['member_liked']),
                    'title' => $username,
                ];
                if (!$done_in_url) {
                    if (strval($type['member_liked']) == $notification_category) {
                        $done_in_url = true;
                    }
                }
            }
        }
        if (!$done_in_url) {
            $page_links[] = [
                'id' => $notification_category,
                'title' => $GLOBALS['FORUM_DRIVER']->get_username(intval($notification_category)),
            ];
        }
        sort_maps_by($page_links, 'title', false, true);

        return array_values($page_links);
    }

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
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority).
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes() : array
    {
        if (!addon_installed('activity_feed')) {
            return [];
        }

        $list = [];
        $list['activity_feed'] = [do_lang('ACTIVITY'), do_lang('activity_feed:NOTIFICATION_TYPE_activity')];
        return $list;
    }
}
