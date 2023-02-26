<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_mappr
 */

/**
 * Block class.
 */
class Block_main_google_map_users
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Kamen / Chris Graham / temp1024';
        $info['organisation'] = 'Miscellaneous';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = ['title', 'region', 'cluster', 'filter_usergroup', 'filter_term', 'geolocate_user', 'username_prefix', 'latitude', 'longitude', 'width', 'height', 'zoom', 'center'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        $map
PHP;
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT;
        $info['ttl'] = 60 * 24 * 7;
        return $info;
    }

    /**
     * Install the block.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        require_code('cpf_install');
        install_gps_fields();
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('user_mappr', $error_msg)) {
            return $error_msg;
        }

        if (get_forum_type() != 'cns') {
            return do_template('RED_ALERT', ['_GUID' => 'rly9gj7499putj5n2emik09qx1h3w1kg', 'TEXT' => do_lang_tempcode('NO_CNS')]);
        }

        require_lang('google_map_users');
        require_lang('locations');

        $block_id = get_block_id($map);

        // Set up config/defaults
        $geolocate_user = array_key_exists('geolocate_user', $map) ? $map['geolocate_user'] : '1';
        if (!array_key_exists('title', $map)) {
            $map['title'] = '';
        }
        if (!array_key_exists('region', $map)) {
            $map['region'] = '';
        }
        if (!array_key_exists('username_prefix', $map)) {
            $map['username_prefix'] = 'Member: ';
        }
        if (!array_key_exists('latitude', $map)) {
            $map['latitude'] = '';
        }
        if (!array_key_exists('longitude', $map)) {
            $map['longitude'] = '';
        }
        $map_width = array_key_exists('width', $map) ? $map['width'] : '100%';
        if (is_numeric($map_width)) {
            $map_width .= 'px';
        }
        $map_height = array_key_exists('height', $map) ? $map['height'] : '300px';
        if (is_numeric($map_height)) {
            $map_height .= 'px';
        }
        $set_zoom = array_key_exists('zoom', $map) ? $map['zoom'] : '3';
        $set_center = array_key_exists('center', $map) ? $map['center'] : '0';
        $cluster = array_key_exists('cluster', $map) ? $map['cluster'] : '0';

        // Ensure installed
        $latitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_latitude']);
        $longitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_longitude']);
        if (($longitude_cpf_id === null) || ($latitude_cpf_id === null)) {
            require_code('zones2');
            reinstall_block('main_google_map_users');

            $latitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_latitude']);
            $longitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_longitude']);
            //return do_template('RED_ALERT', ['TEXT' => 'The maps block has not been installed correctly, the CPFs are missing.']);
        }

        // Data query
        $query = 'SELECT m_username,mf_member_id,m_primary_group,field_' . strval($latitude_cpf_id) . ',field_' . strval($longitude_cpf_id) . ' FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields f ON m.id=f.mf_member_id WHERE field_' . strval($longitude_cpf_id) . ' IS NOT NULL AND field_' . strval($latitude_cpf_id) . ' IS NOT NULL';

        // Filtering
        if (!array_key_exists('filter_usergroup', $map)) {
            $map['filter_usergroup'] = '';
        }
        if ($map['filter_usergroup'] != '') {
            require_code('selectcode');
            $allowed_groups = selectcode_to_idlist_using_memory($map['filter_usergroup'], $GLOBALS['FORUM_DRIVER']->get_usergroup_list());
            $query .= ' AND (';
            foreach ($allowed_groups as $i => $bit) {
                if ($i != 0) {
                    $query .= ' OR ';
                }
                $query .= 'm_primary_group=' . strval($bit);
            }
            $query .= ')';
        }
        if (!array_key_exists('filter_term', $map)) {
            $map['filter_term'] = '';
        }
        if ($map['filter_term'] != '') {
            $query .= ' AND m_username LIKE \'' . db_encode_like('%' . $map['filter_term'] . '%') . '\'';
        }

        // Get results
        $members_to_show = $GLOBALS['FORUM_DB']->query($query);

        if (empty($members_to_show)) { // If there's nothing to show
            if ($geolocate_user == '0') { // Exit, but only if we can't geolocate users via the block (i.e. self-healing)
                return paragraph(do_lang_tempcode('NO_ENTRIES', 'member'), '79mvf9a2ras03j0ohj9a1u60iccvqqpa', 'nothing-here');
            }
        }

        // Make marker data JavaScript-friendly
        $member_data_json = '[';
        foreach ($members_to_show as $i => $member_data) {
            if ($i != 0) {
                $member_data_json .= ',';
            }
            $member_data_json .= '["' . addslashes($GLOBALS['FORUM_DRIVER']->get_displayname($member_data['m_username'])) . '",' .
                float_to_raw_string(@floatval($member_data['field_' . strval($latitude_cpf_id)]), 30) . ',' .
                float_to_raw_string(@floatval($member_data['field_' . strval($longitude_cpf_id)]), 30) . ',' .
                strval($member_data['m_primary_group']) . ']';
        }
        $member_data_json .= ']';

        // See if we need to detect the current user's long/lat
        $member_longitude = @floatval(get_cms_cpf('longitude', get_member()));
        $member_latitude = @floatval(get_cms_cpf('latitude', get_member()));
        $set_coord_url = get_base_url() . '/data_custom/set_coordinates.php?mid=' . strval(get_member()) . '&coord=';
        if (((!empty($member_longitude)) && (!empty($member_latitude))) || (is_guest())) {
            $set_coord_url = '';
        }

        return do_template('BLOCK_MAIN_GOOGLE_MAP_USERS', [
            '_GUID' => '4c80efbd5d31183196ea0f6265f07921',
            'BLOCK_ID' => $block_id,
            'TITLE' => $map['title'],
            'GEOLOCATE_USER' => $geolocate_user,
            'CLUSTER' => $cluster,
            'SET_COORD_URL' => $set_coord_url,
            'REGION' => $map['region'],
            'DATA' => $member_data_json,
            'USERNAME_PREFIX' => $map['username_prefix'],
            'WIDTH' => $map_width,
            'HEIGHT' => $map_height,
            'LATITUDE' => $map['latitude'],
            'LONGITUDE' => $map['longitude'],
            'ZOOM' => $set_zoom,
            'CENTER' => $set_center,
        ]);
    }
}
