<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Composr API helper class.
 */
class CMSBoardStats
{
    /**
     * Get overall forum stats.
     *
     * @return array Map of details
     */
    public function get_board_stat() : array
    {
        cms_verify_parameters_phpdoc();

        return [
            'total_threads' => $this->get_topics_count(),
            'total_posts' => $this->get_posts_count(),
            'total_members' => $this->get_members_count(),
            'active_members' => $this->get_active_members_count(),
            'total_online' => $this->get_online_users_count(),
            'guest_online' => $this->get_online_guests_count(),
        ];
    }

    /**
     * Find number of topics in total.
     *
     * @return integer Total
     */
    private function get_topics_count() : int
    {
        return $GLOBALS['FORUM_DRIVER']->get_num_topics();
    }

    /**
     * Find number of posts in total.
     *
     * @return integer Total
     */
    private function get_posts_count() : int
    {
        return $GLOBALS['FORUM_DRIVER']->get_num_forum_posts();
    }

    /**
     * Find number of members in total.
     *
     * @return integer Total
     */
    private function get_members_count() : int
    {
        return $GLOBALS['FORUM_DRIVER']->get_num_members();
    }

    /**
     * Find number of active members.
     *
     * @return integer Total
     */
    private function get_active_members_count() : int
    {
        $where = ['m_validated_email_confirm_code' => ''];
        if (addon_installed('unvalidated')) {
            $where['m_validated'] = 1;
        }
        return $GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)', $where, ' AND m_last_visit_time>' . strval(time() - 31 * 60 * 60 * 24)); // Active in last month
    }

    /**
     * Find number of online users, includes both members and guests.
     *
     * @return integer Number of online users
     */
    private function get_online_users_count() : int
    {
        $users_online_time_seconds = intval(get_option('users_online_time')) * 60;
        $sql = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'sessions WHERE last_activity>' . strval(time() - $users_online_time_seconds);
        return $GLOBALS['SITE_DB']->query_value_if_there($sql);
    }

    /**
     * Find number of online guests.
     *
     * @return integer Number of online guests
     */
    private function get_online_guests_count() : int
    {
        $users_online_time_seconds = intval(get_option('users_online_time')) * 60;
        $guest_user_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        $sql = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'sessions WHERE last_activity>' . strval(time() - $users_online_time_seconds) . ' AND member_id=' . strval($guest_user_id);
        return $GLOBALS['SITE_DB']->query_value_if_there($sql);
    }
}
