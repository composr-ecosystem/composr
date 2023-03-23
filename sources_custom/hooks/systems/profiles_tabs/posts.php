<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
    public function is_active(int $member_id_of, int $member_id_viewing) : bool
    {
        if (!addon_installed('activity_feed')) {
            return true;
        }

        return (get_value('activities_and_posts') === '1') && (addon_installed('cns_forum'));
    }

    /**
     * Render function for profile tab hooks.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @param  boolean $leave_to_ajax_if_possible Whether to leave the tab contents null, if this hook supports it, so that AJAX can load it later
     * @return array A tuple: The tab title, the tab contents, the suggested tab order, the icon
     */
    public function render_tab(int $member_id_of, int $member_id_viewing, bool $leave_to_ajax_if_possible = false) : array
    {
        $title = do_lang_tempcode('FORUM_POSTS');

        $order = 20;

        if ($leave_to_ajax_if_possible) {
            return [$title, null, $order, 'menu/social/forum/forums'];
        }

        $topics = do_block('main_cns_involved_topics', ['member_id' => strval($member_id_of), 'max' => '10', 'start' => '0']);
        $content = do_template('CNS_MEMBER_PROFILE_POSTS', ['MEMBER_ID' => strval($member_id_of), 'TOPICS' => $topics]);

        return [$title, $content, $order, 'menu/social/forum/forums'];
    }
}
