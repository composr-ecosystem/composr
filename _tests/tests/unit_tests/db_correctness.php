<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class db_correctness_test_set extends cms_test_case
{
    protected $files, $table_fields, $all_fields, $tables, $indices;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        require_code('files2');
        $this->files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | (in_safe_mode() ? IGNORE_NONBUNDLED : 0), true, true, array('php'));
        $this->files[] = 'install.php';

        $db_fields = $GLOBALS['SITE_DB']->query_select('db_meta', array('m_table', 'm_name'));

        $db_indices = $GLOBALS['SITE_DB']->query_select('db_meta_indices', array('i_table', 'i_name'));

        $this->table_fields = array('site' => array(), 'forum' => array());
        foreach ($db_fields as $db_field) {
            $forum = $this->is_forum_table($db_field['m_table']);
            if ($forum === null) {
                continue;
            }
            $this->table_fields[$forum ? 'forum' : 'site'][] = $db_field['m_name'];
        }
        $this->table_fields['_site'] = array_diff($this->table_fields['site'], $this->table_fields['forum']);
        $this->table_fields['site_regexp'] = implode('|', $this->table_fields['_site']);
        $this->table_fields['_forum'] = array_diff($this->table_fields['forum'], $this->table_fields['site']);
        $this->table_fields['forum_regexp'] = implode('|', $this->table_fields['_forum']);

        $this->all_fields = array();
        foreach ($db_fields as $field) {
            $table = $field['m_table'];
            $name = $field['m_name'];

            if (!isset($this->all_fields[$table])) {
                $this->all_fields[$table] = array();
            }
            $this->all_fields[$table][] = $name;
        }

        $this->tables = array();
        foreach (array_unique(collapse_1d_complexity('m_table', $db_fields)) as $table) {
            $forum = $this->is_forum_table($table);
            if ($forum === null) {
                continue;
            }
            $this->tables[$forum ? 'forum' : 'site'][] = $table;
        }
        $this->tables['site_regexp'] = implode('|', $this->tables['site']);
        $this->tables['forum_regexp'] = implode('|', $this->tables['forum']);

        $this->indexes = array('site' => array(), 'forum' => array());
        foreach ($db_indices as $db_field) {
            $forum = $this->is_forum_table($db_field['i_table']);
            if ($forum === null) {
                continue;
            }
            $this->indexes[$forum ? 'forum' : 'site'][] = ($db_field['i_name'][0] == '#') ? substr($db_field['i_name'], 1) : $db_field['i_name'];
        }
        $this->indexes['_site'] = array_diff($this->indexes['site'], $this->indexes['forum']);
        $this->indexes['site_regexp'] = implode('|', $this->indexes['_site']);
        $this->indexes['_forum'] = array_diff($this->indexes['forum'], $this->indexes['site']);
        $this->indexes['forum_regexp'] = implode('|', $this->indexes['_forum']);
    }

    public function testNoBadTablePrefixing()
    {
        $php_path = find_php_path();

        foreach ($this->files as $path) {
            // Exceptions
            if (in_array($path, array(
                'sources/forum/none.php',
                'sources/users.php',
                '_tests/tests/unit_tests/db_correctness.php',
                'adminzone/pages/modules/admin_version.php',
                'sources/blocks/main_friends_list.php',
                'sources/hooks/modules/admin_setupwizard/cns_forum.php',
                'sources/hooks/systems/cron/subscription_mails.php',
                'sources/upgrade_db_upgrade.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $this->assertTrue(strpos($c, ". get_table_prefix() . 'f_") === false, 'Wrong forum table prefix in ' . $path);
            $this->assertTrue(strpos($c, ". \$GLOBALS['SITE_DB']->get_table_prefix() . 'f_") === false, 'Wrong forum table prefix in ' . $path);

            $matches = array();
            $num_matches = preg_match_all('#\$GLOBALS\[\'FORUM_DB\'\]->get_table_prefix\(\) . \'(\w+)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $table = $matches[1][$i];
                $forum = $this->is_forum_table($table);
                $this->assertTrue(($forum === null) || $forum, 'Found a non-forum table (' . $table . ') with a forum table prefix on ' . $path);
            }
        }
    }

    public function testGetTranslatedTempcodeMatchingFieldsAndTables()
    {
        foreach ($this->files as $path) {
            $c = cms_file_get_contents_safe($path);

            $matches = array();
            $num_matches = preg_match_all('#get_translated_tempcode\(\'(\w+)\', [^,]*, \'(\w+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $table = $matches[1][$i];
                $name = $matches[2][$i];

                $ok = (isset($this->all_fields[$table])) && (in_array($name, $this->all_fields[$table]));
                $this->assertTrue($ok, 'Could not find ' . $table . ':' . $name . ', ' . $path);
            }
        }
    }

    public function testGetTranslatedRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['forum_regexp'] . ')\', [^,]*, [^,]*' . preg_quote(", \$GLOBALS['SITE_DB']", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['site_regexp'] . ')\', [^,]*, [^,]*' . preg_quote(", \$GLOBALS['FORUM_DB']", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testTranslateFieldRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->translate_field_ref('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->translate_field_ref('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("')", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testTableIsLockedRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->table_is_locked('", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->table_is_locked('", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("')", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testPreferIndexLockedRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->prefer_index('", '#') . "[^']+" . preg_quote("', '", '#') . '(' . $this->indexes['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->prefer_index('", '#') . "[^']+" . preg_quote("', '", '#') . '(' . $this->indexes['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testInsertLangRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'SITE_DB\'\]' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,\(\)]*, [^,\(\)]*\)' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'FORUM_DB\'\]' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testUpdateLangRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'SITE_DB\'\]' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,\(\)]*, [^,\(\)]*\)' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'FORUM_DB\'\]' . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testSelectRef()
    {
        foreach ($this->files as $file) {
            // Exceptions
            if (in_array($file, array(
                'sources/forum/none.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select_value(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select_value(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select_value_if_there(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select_value_if_there(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testInsertRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_insert(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_insert(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testUpdateRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_update(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_update(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testDeleteRef()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_delete(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_delete(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testCreateRef()
    {
        foreach ($this->files as $file) {
            // Exceptions
            if (in_array($file, array(
                'site/pages/modules/subscriptions.php',
                'adminzone/pages/modules/admin_version.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->create_table(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->create_table(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testDropRef()
    {
        foreach ($this->files as $file) {
            // Exceptions
            if (in_array($file, array(
                'site/pages/modules/subscriptions.php',
                'adminzone/pages/modules/admin_version.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->drop_table_if_exists(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->drop_table_if_exists(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testCreateIndexRef()
    {
        foreach ($this->files as $file) {
            // Exceptions
            if (in_array($file, array(
                'site/pages/modules/subscriptions.php',
                'adminzone/pages/modules/admin_version.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->create_index(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->create_index(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testDeleteIndexRef()
    {
        foreach ($this->files as $file) {
            // Exceptions
            if (in_array($file, array(
                'site/pages/modules/subscriptions.php',
                'adminzone/pages/modules/admin_version.php',
            ))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->delete_index_if_exists(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->delete_index_if_exists(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testJoinConsistency()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            if ($file == '_tests/tests/unit_tests/db_correctness.php') {
                continue;
            }

            if ($file != 'sources/users.php') {
                $bad_pattern = '#SITE_DB.*JOIN .*\'f_#';
                $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
            }

            $bad_pattern = '#FORUM_DB.*JOIN .*\'(' . $this->tables['site_regexp'] . ')#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    public function testForumDbForumDriverMixup()
    {
        foreach ($this->files as $file) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $driver_funcs = array(
                'cns_flood_control',
                'find_emoticons',
                'find_topic_id_for_topic_identifier',
                'forum_authorise_login',
                'forum_create_cookie',
                'forum_get_lang',
                'forum_id_from_name',
                'forum_layer_initialise',
                'forum_md5',
                'forum_url',
                'get_custom_fields',
                'get_displayname',
                'get_drivered_table_prefix',
                'get_emoticon_chooser',
                'get_emo_dir',
                'get_forum_topic_posts',
                'get_guest_group',
                'get_guest_id',
                'get_matching_members',
                'get_members_groups',
                'get_member_avatar_url',
                'get_member_email_address',
                'get_member_email_allowed',
                'get_member_from_email_address',
                'get_member_from_username',
                'get_member_ip',
                'get_member_join_timestamp',
                'get_member_photo_url',
                'get_member_row',
                'get_member_row_field',
                'get_moderator_groups',
                'get_mrow',
                'get_next_member',
                'get_num_forum_posts',
                'get_num_members',
                'get_num_new_forum_posts',
                'get_num_topics',
                'get_num_users_forums',
                'get_post_count',
                'get_post_remaining_details',
                'get_previous_member',
                'get_super_admin_groups',
                'get_theme',
                'get_topic_count',
                'get_top_posters',
                'get_usergroup_list',
                'get_username',
                'install_create_custom_field',
                'install_delete_custom_field',
                'install_get_path_search_list',
                'install_specifics',
                'install_test_load_from',
                'is_banned',
                'is_cookie_login_name',
                'is_hashed',
                'is_staff',
                'is_super_admin',
                'join_url',
                'make_post_forum_topic',
                'member_group_query',
                'member_home_url',
                'member_pm_url',
                'member_profile_hyperlink',
                'member_profile_url',
                'mrow_email',
                'mrow_group',
                'mrow_id',
                'mrow_lastvisit',
                'mrow_username',
                'pin_topic',
                'post_url',
                'probe_ip',
                'set_custom_field',
                'show_forum_topics',
                'topic_is_threaded',
                'topic_url',
                'users_online_url',
                '_get_theme',
            );
            $bad_pattern = '#\$GLOBALS\[\'FORUM_DB\'\]->(' . implode('|', $driver_funcs) . ')\(#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);

            $db_funcs = array(
                'query.*',
            );
            $bad_pattern = '#\$GLOBALS\[\'FORUM_DRIVER\'\]->(' . implode('|', $db_funcs) . ')\(#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $file);
        }
    }

    protected function is_forum_table($table)
    {
        if (in_array($table, array(
            'group_privileges', 'member_privileges', 'group_category_access', 'member_category_access',
            'attachments', 'attachment_refs',
            'notifications_enabled',
            'translate',
            'member_tracking',
            'custom_comcode',
        ))) {
            return null;
        }

        return (substr($table, 0, 2) == 'f_') && ($table != 'f_welcome_emails');
    }
}
