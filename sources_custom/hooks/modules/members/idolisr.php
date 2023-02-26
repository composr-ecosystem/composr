<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    idolisr
 */

/**
 * Hook class.
 */
class Hook_members_idolisr
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function get_tracking_details(int $member_id) : array
    {
        if (!addon_installed('idolisr')) {
            return [];
        }

        $topics_opened = $GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', ['t_cache_first_member_id' => $member_id]);
        $num_replies = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'COUNT(DISTINCT p_topic_id)', ['p_poster' => $member_id]) - $topics_opened;
        return ['Forum contributions' => $GLOBALS['FORUM_DRIVER']->get_username($member_id, true) . ' has opened ' . integer_format($topics_opened) . ' ' . (($topics_opened == 1) ? 'topic' : 'topics') . ' and replied to ' . integer_format($num_replies) . ' ' . (($num_replies == 1) ? 'topic' : 'topics') . ' by other people.'];
    }
}
