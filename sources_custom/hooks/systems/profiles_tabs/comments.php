<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function is_active($member_id_of, $member_id_viewing)
    {
        return true;
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
        require_lang('member_comments');

        $title = do_lang_tempcode('MEMBER_COMMENTS');

        $order = 25;

        if ($leave_to_ajax_if_possible && !has_interesting_post_fields()) {
            return array($title, null, $order, 'feedback/comment');
        }

        $forum_name = do_lang('MEMBER_COMMENTS_FORUM_NAME');
        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name($forum_name);
        if (is_null($forum_id)) {
            require_code('cns_forums_action');

            $forum_grouping_id = $GLOBALS['FORUM_DB']->query_select_value('f_forum_groupings', 'MIN(id)');

            $val = $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'AVG(f_allows_anonymous_posts)');
            $allows_anonymous_posts = is_null($val) ? 1 : intval(round($val));

            $forum_id = cns_make_forum($forum_name, '', $forum_grouping_id, array(), db_get_first_id()/*parent*/, 20/*position*/, 1, 1, '', '', '', 'last_post', 1/*is threaded*/, $allows_anonymous_posts);
        }

        // The member who 'owns' the tab should be receiving notifications
        require_code('notifications');
        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id_of);
        $main_map = array(
            'l_member_id' => $member_id_of,
            'l_notification_code' => 'comment_posted',
            'l_code_category' => 'block_main_comments_' . $username . '_member',
        );
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('notifications_enabled', 'id', $main_map);
        if (is_null($test)) {
            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', array(
                'l_setting' => _find_member_statistical_notification_type($member_id_of, 'comment_posted'),
            ) + $main_map);
        }

        $content = do_template('CNS_MEMBER_PROFILE_COMMENTS', array('_GUID' => '5ce1949e4fa0d247631f52f48698df4e', 'MEMBER_ID' => strval($member_id_of), 'FORUM_NAME' => $forum_name));
        $content->handle_symbol_preprocessing();

        return array($title, $content, $order, 'feedback/comment');
    }
}
