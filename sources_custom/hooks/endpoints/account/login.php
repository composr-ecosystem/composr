<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set).
     * @param  ?string $id Standard ID parameter (null: not-set).
     * @return array Data structure that will be converted to correct response type.
     */
    public function run($type, $id)
    {
        $username = trim(post_param_string('username'));
        $password = trim(post_param_string('password'));

        $feedback = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, apply_forum_driver_md5_variant($password, $username), $password);
        $member_id = $feedback['id'];
        if (is_null($member_id)) {
            warn_exit($feedback['error']);
        }

        require_code('cns_general');
        require_code('users_active_actions');

        cms_setcookie(get_member_cookie(), strval($member_id));
        $password_hashed_salted = $GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_pass_hash_salted');
        $password_compat_scheme = $GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_password_compat_scheme');
        if ($password_compat_scheme == 'plain') { // can't do direct representation for this, would be a plain text cookie; so in forum_authorise_login we expect it to be md5'd and compare thusly (as per non-cookie call to that function)
            $password_hashed_salted = md5($password_hashed_salted);
        }
        cms_setcookie(get_pass_cookie(), $password_hashed_salted);

        $data = cns_read_in_member_profile($member_id, false);

        $_groups = $data['groups'];
        unset($data['groups']);

        require_code('permissions');
        $where_groups = _get_where_clause_groups($member_id);
        if (is_null($where_groups)) {
            $privileges_perhaps = $GLOBALS['SITE_DB']->query_select('privilege_list', array('the_name AS privilege'));
            $pages_blacklist = array();
            $zones_perhaps = $GLOBALS['SITE_DB']->query_select('zones', array('zone_name'));
        } else {
            $where = ' AND ' . db_string_equal_to('the_page', '');
            $where .= ' AND ' . db_string_equal_to('module_the_name', '');
            $where .= ' AND the_value=1';
            $sql = 'SELECT privilege FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_privileges WHERE (' . $where_groups . ')' . $where;
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT privilege FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_privileges WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')' . $where;
            $privileges_perhaps = $GLOBALS['SITE_DB']->query($sql, null, null, false, true);

            $sql = 'SELECT page_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_page_access WHERE (' . $where_groups . ')';
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT page_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_page_access WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')';
            $pages_blacklist = $GLOBALS['SITE_DB']->query($sql, null, null, false, true);

            $sql = 'SELECT zone_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_zone_access WHERE (' . $where_groups . ')';
            $sql .= ' UNION ALL ';
            $sql .= 'SELECT zone_name FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'member_zone_access WHERE member_id=' . strval($member_id) . ' AND (active_until IS NULL OR active_until>' . strval(time()) . ')';
            $zones_perhaps = $GLOBALS['SITE_DB']->query($sql, null, null, false, true);
        }

        $groups = array();
        foreach (array_keys($_groups) as $group_id) {
            $groups[] = array(
                'id' => $group_id,
                'name' => cns_get_group_name($group_id),
            );
        }

        $data += array(
            'memberID' => $member_id,
            'privileges' => collapse_1d_complexity('privilege', $privileges_perhaps),
            'pages_blacklist' => collapse_1d_complexity('page_name', $pages_blacklist),
            'zone_access' => collapse_1d_complexity('zone_name', $zones_perhaps),
            'staff_status' => $GLOBALS['FORUM_DRIVER']->is_staff($member_id),
            'admin_status' => $GLOBALS['FORUM_DRIVER']->is_super_admin($member_id),
            'sessionID' => get_session_id(),
            'groups' => $groups,
            'password' => $password,

            'device_auth_member_id_cn' => get_member_cookie(),
            'device_auth_pass_hashed_cn' => get_pass_cookie(),
            'device_auth_member_id_vl' => strval($member_id),
            'device_auth_pass_hashed_vl' => $password_hashed_salted,
        );

        return $data;
    }
}
