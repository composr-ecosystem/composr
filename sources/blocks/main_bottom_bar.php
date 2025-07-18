<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_forum
 */

/**
 * Block class.
 */
class Block_main_bottom_bar
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array();
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        if (get_forum_type() != 'cns') {
            return new Tempcode();
        }

        if (!isset($GLOBALS['FORUM_DRIVER'])) {
            return new Tempcode();
        }

        require_code('cns_general');
        require_code('cns_groups');
        require_css('cns');
        require_css('cns_footer');

        require_lang('cns');

        $stats = cns_get_forums_stats();

        // Users online
        $users_online = new Tempcode();
        $count = 0;
        require_code('users2');
        $members = get_users_online(false, null, $count);
        $groups_seen = array();
        $num_members = 0;
        $num_guests = 0;
        if (!is_null($members)) {
            foreach ($members as $bits) {
                $member = $bits['member_id'];
                $username = $bits['cache_username'];

                if ($member == $GLOBALS['CNS_DRIVER']->get_guest_id()) {
                    $num_guests++;
                    continue;
                }
                if (is_null($username)) {
                    continue;
                }
                $url = $GLOBALS['CNS_DRIVER']->member_profile_url($member, false, true);
                if (!array_key_exists('m_primary_group', $bits)) {
                    $bits['m_primary_group'] = $GLOBALS['FORUM_DRIVER']->get_member_row_field($member, 'm_primary_group');
                }
                $pgid = $bits['m_primary_group'];//$GLOBALS['FORUM_DRIVER']->get_member_row_field($member,'m_primary_group');
                if (is_null($pgid)) {
                    continue; // Deleted member
                }
                $groups_seen[$pgid] = 1;
                $col = get_group_colour($pgid);
                $usergroup = cns_get_group_name($pgid);
                if (get_option('enable_user_online_groups') == '0') {
                    $usergroup = null;
                    $col = null;
                    $groups_seen = array();
                }
                $users_online->attach(do_template('CNS_USER_MEMBER', array('_GUID' => 'a9cb1af2a04b14edd70749c944495bff', 'FIRST' => $num_members == 0, 'COLOUR' => $col, 'PROFILE_URL' => $url, 'USERNAME' => $username, 'USERGROUP' => $usergroup)));
                $num_members++;
            }
            if ($num_guests != 0) {
                if (!$users_online->is_empty() && do_lang('NUM_GUESTS', 'xxx') != '') {
                    $users_online->attach(do_lang_tempcode('LIST_SEP'));
                    $users_online->attach(do_lang_tempcode('NUM_GUESTS', escape_html(integer_format($num_guests))));
                }
            }
        }

        // Birthdays
        $birthdays = array();
        if (get_option('enable_birthdays') != '0') {
            $_birthdays = cns_find_birthdays();
            foreach ($_birthdays as $_birthday) {
                $birthday_url = build_url(array('page' => 'topics', 'type' => 'birthday', 'id' => $_birthday['username']), get_module_zone('topics'));
                $birthdays[] = array(
                    'AGE' => array_key_exists('age', $_birthday) ? integer_format($_birthday['age']) : null,
                    'PROFILE_URL' => $GLOBALS['CNS_DRIVER']->member_profile_url($_birthday['id'], false, true),
                    'USERNAME' => $_birthday['username'],
                    'MEMBER_ID' => strval($_birthday['id']),
                    'BIRTHDAY_URL' => $birthday_url,
                );
            }
        }

        // Usergroup keys
        $groups = array();
        $all_groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(true, false, false, null, null, true);
        foreach ($all_groups as $gid => $gtitle) {
            if ($gid == db_get_first_id()) {
                continue; // Throw out the first, guest
            }
            if (array_key_exists($gid, $groups_seen)) {
                $groups[] = array('GCOLOUR' => get_group_colour($gid), 'GID' => strval($gid), 'GTITLE' => $gtitle);
            }
        }

        return do_template('BLOCK_MAIN_BOTTOM_BAR', array(
            '_GUID' => 'sdflkdlfd303frksdf',
            'NEWEST_MEMBER_PROFILE_URL' => $GLOBALS['CNS_DRIVER']->member_profile_url($stats['newest_member_id'], false, true),
            'NEWEST_MEMBER_USERNAME' => $stats['newest_member_username'],
            'NUM_MEMBERS' => integer_format($stats['num_members']),
            'NUM_TOPICS' => integer_format($stats['num_topics']),
            'NUM_POSTS' => integer_format($stats['num_posts']),
            'BIRTHDAYS' => $birthdays,
            'USERS_ONLINE' => $users_online,
            'USERS_ONLINE_URL' => has_actual_page_access(get_member(), 'users_online') ? build_url(array('page' => 'users_online'), get_module_zone('users_online')) : new Tempcode(),
            'GROUPS' => $groups,
            '_NUM_GUESTS' => strval($num_guests),
            '_NUM_MEMBERS' => strval($num_members),
        ));
    }
}
