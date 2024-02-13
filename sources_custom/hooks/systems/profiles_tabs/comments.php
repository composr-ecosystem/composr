<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    member_comments
 */

/**
 * Hook class.
 */
class Hook_profiles_tabs_comments
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
        if (!addon_installed('member_comments')) {
            return false;
        }

        $forum_name = get_option('member_comments_forum_name');
        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name($forum_name);
        return $forum_id !== null;
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
        require_lang('member_comments');

        $title = do_lang_tempcode('MEMBER_COMMENTS');

        $order = 25;

        if ($leave_to_ajax_if_possible && !has_interesting_post_fields()) {
            return [$title, null, $order, 'feedback/comment'];
        }

        $forum_name = get_option('member_comments_forum_name');
        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name($forum_name);

        // The member who 'owns' the tab should be receiving notifications
        require_code('notifications');
        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id_of);
        $main_map = [
            'l_member_id' => $member_id_of,
            'l_notification_code' => 'comment_posted',
            'l_code_category' => 'block_main_comments_' . $username . '_member',
        ];
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('notifications_enabled', 'id', $main_map);
        if ($test === null) {
            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', [
                'l_setting' => _find_member_statistical_notification_type($member_id_of, 'comment_posted'),
            ] + $main_map);
        }

        $content = do_template('CNS_MEMBER_PROFILE_COMMENTS', ['_GUID' => '5ce1949e4fa0d247631f52f48698df4e', 'MEMBER_ID' => strval($member_id_of), 'FORUM_ID' => strval($forum_id)]);
        $content->handle_symbol_preprocessing();

        return [$title, $content, $order, 'feedback/comment'];
    }
}
