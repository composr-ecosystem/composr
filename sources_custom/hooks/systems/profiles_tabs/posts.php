<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_profiles_tabs_posts
{
    /**
     * Find whether this hook is active.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @return boolean Whether this hook is active
     */
    public function is_active($member_id_of, $member_id_viewing)
    {
        return (get_value('activities_and_posts') === '1') && (addon_installed('cns_forum'));
    }

    /**
     * Render function for profile tab hooks.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @param  boolean $leave_to_ajax_if_possible Whether to leave the tab contents null, if tis hook supports it, so that AJAX can load it later
     * @return array A tuple: The tab title, the tab contents, the suggested tab order, the icon
     */
    public function render_tab($member_id_of, $member_id_viewing, $leave_to_ajax_if_possible = false)
    {
        $title = do_lang_tempcode('FORUM_POSTS');

        $order = 20;

        if ($leave_to_ajax_if_possible) {
            return array($title, null, $order, 'menu/social/forum/forums');
        }

        $topics = do_block('main_cns_involved_topics', array('member_id' => strval($member_id_of), 'max' => '10', 'start' => '0'));
        $content = do_template('CNS_MEMBER_PROFILE_POSTS', array('_GUID' => '5f2369da74c17bc375df1599a2aaad8a', 'MEMBER_ID' => strval($member_id_of), 'TOPICS' => $topics));

        return array($title, $content, $order, 'menu/social/forum/forums');
    }
}
