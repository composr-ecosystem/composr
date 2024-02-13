<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_endpoint_account_login
{
    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('composr_mobile_sdk')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $username = post_param_string('username', false, INPUT_FILTER_POST_IDENTIFIER);
        $password = post_param_string('password', false, INPUT_FILTER_POST_IDENTIFIER);

        $feedback = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
        $member_id = $feedback['id'];
        if ($member_id === null) {
            warn_exit($feedback['error']);
        }

        require_code('cns_general');

        require_code('cns_forum_driver_helper_auth');
        cns_create_login_cookie($member_id);

        push_query_limiting(false);

        $data = cns_read_in_member_profile($member_id);

        $_groups = $data['groups'];
        unset($data['groups']);

        require_code('permissions');
        $where_groups = get_permission_where_clause_groups($member_id);
        if ($where_groups === null) {
            $privileges_perhaps = $GLOBALS['SITE_DB']->query_select('privilege_list', ['the_name AS privilege']);
            $pages_exclusion_list = [];
            $zones_perhaps = $GLOBALS['SITE_DB']->query_select('zones', ['zone_name']);
        } else {
            $where = ' AND ' . db_string_equal_to('the_page', '');
            $where .= ' AND ' . db_string_equal_to('module_the_name', '');
            $where .= ' AND the_value=1';
            $sql = 'SELECT privilege FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_privileges WHERE (' . $where_groups . ')' . $where;
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT privilege FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_privileges WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')' . $where;
            $privileges_perhaps = $GLOBALS['SITE_DB']->query($sql, null, 0, false, true);

            $sql = 'SELECT page_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_page_access WHERE (' . $where_groups . ')';
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT page_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_page_access WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')';
            $pages_exclusion_list = $GLOBALS['SITE_DB']->query($sql, null, 0, false, true);

            $sql = 'SELECT zone_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_zone_access WHERE (' . $where_groups . ')';
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT zone_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_zone_access WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')';
            $zones_perhaps = $GLOBALS['SITE_DB']->query($sql, null, 0, false, true);
        }

        $groups = [];
        foreach (array_keys($_groups) as $group_id) {
            $groups[] = [
                'id' => $group_id,
                'name' => cns_get_group_name($group_id),
            ];
        }

        $login_key = $GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_login_key');
        if ($login_key == '') {
            require_code('crypt');
            $login_key = get_secure_random_string();
            $GLOBALS['FORUM_DB']->query_update('f_members', ['m_login_key' => $login_key], ['id' => $member_id], '', 1);
        }

        $data += [
            'memberID' => $member_id,
            'privileges' => collapse_1d_complexity('privilege', $privileges_perhaps),
            'pages_exclusion_list' => collapse_1d_complexity('page_name', $pages_exclusion_list),
            'zone_access' => collapse_1d_complexity('zone_name', $zones_perhaps),
            'staff_status' => $GLOBALS['FORUM_DRIVER']->is_staff($member_id),
            'admin_status' => $GLOBALS['FORUM_DRIVER']->is_super_admin($member_id),
            'sessionID' => get_session_id(),
            'groups' => $groups,
            'password' => $password,

            'device_auth_member_id_cn' => get_member_cookie(),
            'device_auth_pass_hashed_cn' => get_pass_cookie(),
            'device_auth_member_id_vl' => strval($member_id),
            'device_auth_pass_hashed_vl' => $login_key,
        ];

        return $data;
    }
}
