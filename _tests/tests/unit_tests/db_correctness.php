<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
    protected $table_fields;
    protected $all_fields;
    protected $tables;
    protected $indexes;

    public function testDbCorrectness()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | (in_safe_mode() ? IGNORE_NONBUNDLED : 0), true, true, ['php']);
        $files[] = 'install.php';

        $db_fields = $GLOBALS['SITE_DB']->query_select('db_meta', ['m_table', 'm_name']);

        $db_indices = $GLOBALS['SITE_DB']->query_select('db_meta_indices', ['i_table', 'i_name']);

        $table_fields = ['site' => [], 'forum' => []];
        foreach ($db_fields as $db_field) {
            $forum = $this->is_forum_table($db_field['m_table']);
            if ($forum === null) {
                continue;
            }
            $table_fields[$forum ? 'forum' : 'site'][] = $db_field['m_name'];
        }
        $table_fields['_site'] = array_diff($table_fields['site'], $table_fields['forum']);
        $table_fields['site_regexp'] = implode('|', $table_fields['_site']);
        $table_fields['_forum'] = array_diff($table_fields['forum'], $table_fields['site']);
        $table_fields['forum_regexp'] = implode('|', $table_fields['_forum']);
        $this->table_fields = $table_fields;

        $all_fields = [];
        foreach ($db_fields as $field) {
            $table = $field['m_table'];
            $name = $field['m_name'];

            if (!isset($all_fields[$table])) {
                $all_fields[$table] = [];
            }
            $all_fields[$table][] = $name;
        }
        $this->all_fields = $all_fields;

        $tables = [];
        foreach (array_unique(collapse_1d_complexity('m_table', $db_fields)) as $table) {
            $forum = $this->is_forum_table($table);
            if ($forum === null) {
                continue;
            }
            $tables[$forum ? 'forum' : 'site'][] = $table;
        }
        $tables['site_regexp'] = implode('|', $tables['site']);
        $tables['forum_regexp'] = implode('|', $tables['forum']);
        $this->tables = $tables;

        $indexes = ['site' => [], 'forum' => []];
        foreach ($db_indices as $db_field) {
            $forum = $this->is_forum_table($db_field['i_table']);
            if ($forum === null) {
                continue;
            }
            $indexes[$forum ? 'forum' : 'site'][] = ($db_field['i_name'][0] == '#') ? substr($db_field['i_name'], 1) : $db_field['i_name'];
        }
        $indexes['_site'] = array_diff($indexes['site'], $indexes['forum']);
        $indexes['site_regexp'] = implode('|', $indexes['_site']);
        $indexes['_forum'] = array_diff($indexes['forum'], $indexes['site']);
        $indexes['forum_regexp'] = implode('|', $indexes['_forum']);
        $this->indexes = $indexes;

        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $this->_testNoBadTablePrefixing($path, $c);
            $this->_testGetTranslatedTempcodeMatchingFieldsAndTables($path, $c);
            $this->_testGetTranslatedRef($path, $c);
            $this->_testTranslateFieldRef($path, $c);
            $this->_testTableIsLockedRef($path, $c);
            $this->_testPreferIndexLockedRef($path, $c);
            $this->_testInsertLangRef($path, $c);
            $this->_testUpdateLangRef($path, $c);
            $this->_testSelectRef($path, $c);
            $this->_testInsertRef($path, $c);
            $this->_testUpdateRef($path, $c);
            $this->_testDeleteRef($path, $c);
            $this->_testCreateRef($path, $c);
            $this->_testDropRef($path, $c);
            $this->_testCreateIndexRef($path, $c);
            $this->_testDeleteIndexRef($path, $c);
            $this->_testJoinConsistency($path, $c);
            $this->_testForumDbForumDriverMixup($path, $c);
        }
    }

    protected function _testNoBadTablePrefixing($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'sources/forum/none.php',
            'sources/users.php',
            '_tests/tests/unit_tests/db_correctness.php',
            'adminzone/pages/modules/admin_version.php',
            'sources/blocks/main_friends_list.php',
            'sources/hooks/modules/admin_setupwizard/cns_forum.php',
            'sources/hooks/systems/cron/subscription_mails.php',
            'sources/upgrade_db_upgrade.php',
        ])) {
            return;
        }

        $this->assertTrue(strpos($c, ". get_table_prefix() . 'f_") === false, 'Wrong forum table prefix in ' . $path);
        $this->assertTrue(strpos($c, ". \$GLOBALS['SITE_DB']->get_table_prefix() . 'f_") === false, 'Wrong forum table prefix in ' . $path);

        $matches = [];
        $num_matches = preg_match_all('#\$GLOBALS\[\'FORUM_DB\'\]->get_table_prefix\(\) . \'(\w+)#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $table = $matches[1][$i];
            $forum = $this->is_forum_table($table);
            $this->assertTrue(($forum === null) || $forum, 'Found a non-forum table (' . $table . ') with a forum table prefix on ' . $path);
        }
    }

    protected function _testGetTranslatedTempcodeMatchingFieldsAndTables($path, $c)
    {
        $matches = [];
        $num_matches = preg_match_all('#get_translated_tempcode\(\'(\w+)\', [^,]*, \'(\w+)\'\)#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $table = $matches[1][$i];
            $name = $matches[2][$i];

            $ok = (isset($this->all_fields[$table])) && (in_array($name, $this->all_fields[$table]));
            $this->assertTrue($ok, 'Could not find ' . $table . ':' . $name . ', ' . $path);
        }
    }

    protected function _testGetTranslatedRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['forum_regexp'] . ')\', [^,]*, [^,]*' . preg_quote(", \$GLOBALS['SITE_DB']", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("get_translated_tempcode('", '#') . '(' . $this->tables['site_regexp'] . ')\', [^,]*, [^,]*' . preg_quote(", \$GLOBALS['FORUM_DB']", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testTranslateFieldRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->translate_field_ref('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->translate_field_ref('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("')", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testTableIsLockedRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->table_is_locked('", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("')", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->table_is_locked('", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("')", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testPreferIndexLockedRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->prefer_index('", '#') . "[^']+" . preg_quote("', '", '#') . '(' . $this->indexes['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->prefer_index('", '#') . "[^']+" . preg_quote("', '", '#') . '(' . $this->indexes['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testInsertLangRef($path, $c)
    {
        $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'SITE_DB\'\]' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,\(\)]*, [^,\(\)]*\)' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#(insert_lang|insert_lang_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'FORUM_DB\'\]' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testUpdateLangRef($path, $c)
    {
        $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'SITE_DB\'\]' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['forum_regexp'] . ')' . preg_quote("'", '#') . ', [^,\(\)]*, [^,\(\)]*\)' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#(lang_remap|lang_remap_comcode)' . preg_quote("('", '#') . '(' . $this->table_fields['site_regexp'] . ')' . preg_quote("'", '#') . ', [^,]*, [^,]*, \$GLOBALS\[\'FORUM_DB\'\]' . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testSelectRef($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'sources/forum/none.php',
        ])) {
            return;
        }

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select_value(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select_value(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_select_value_if_there(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_select_value_if_there(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testInsertRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_insert(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_insert(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testUpdateRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_update(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_update(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testDeleteRef($path, $c)
    {
        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->query_delete(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->query_delete(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testCreateRef($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'site/pages/modules/subscriptions.php',
            'adminzone/pages/modules/admin_version.php',
        ])) {
            return;
        }

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->create_table(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->create_table(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testDropRef($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'site/pages/modules/subscriptions.php',
            'adminzone/pages/modules/admin_version.php',
        ])) {
            return;
        }

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->drop_table_if_exists(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->drop_table_if_exists(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testCreateIndexRef($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'site/pages/modules/subscriptions.php',
            'adminzone/pages/modules/admin_version.php',
        ])) {
            return;
        }

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->create_index(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->create_index(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testDeleteIndexRef($path, $c)
    {
        // Exceptions
        if (in_array($path, [
            'site/pages/modules/subscriptions.php',
            'adminzone/pages/modules/admin_version.php',
        ])) {
            return;
        }

        $bad_pattern = '#' . preg_quote("\$GLOBALS['SITE_DB']->delete_index_if_exists(\s*'", '#') . '(' . $this->tables['forum_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $bad_pattern = '#' . preg_quote("\$GLOBALS['FORUM_DB']->delete_index_if_exists(\s*'", '#') . '(' . $this->tables['site_regexp'] . ')' . preg_quote("'", '#') . '#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testJoinConsistency($path, $c)
    {
        if ($path == '_tests/tests/unit_tests/db_correctness.php') {
            return;
        }

        if ($path != 'sources/users.php') {
            $bad_pattern = '#SITE_DB.*JOIN .*\'f_#';
            $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
        }

        $bad_pattern = '#FORUM_DB.*JOIN .*\'(' . $this->tables['site_regexp'] . ')#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function _testForumDbForumDriverMixup($path, $c)
    {
        $driver_funcs = [
            'cns_flood_control',
            'find_emoticons',
            'find_topic_id_for_topic_identifier',
            'authorise_login',
            'create_login_cookie',
            'get_lang',
            'forum_id_from_name',
            'forum_layer_initialise',
            'password_hash',
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
            'get_next_members',
            'get_num_forum_posts',
            'get_num_members',
            'get_num_new_forum_posts',
            'get_num_topics',
            'get_num_users_forums',
            'get_post_count',
            'get_post_remaining_details',
            'get_previous_members',
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
            'is_staff',
            'is_super_admin',
            'join_url',
            'make_post_forum_topic',
            'member_group_query',
            'member_home_url',
            'member_pm_url',
            'member_profile_hyperlink',
            'member_profile_url',
            'mrow_email_address',
            'mrow_primary_group',
            'mrow_member_id',
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
        ];
        $bad_pattern = '#\$GLOBALS\[\'FORUM_DB\'\]->(' . implode('|', $driver_funcs) . ')\(#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);

        $db_funcs = [
            'query.*',
        ];
        $bad_pattern = '#\$GLOBALS\[\'FORUM_DRIVER\'\]->(' . implode('|', $db_funcs) . ')\(#';
        $this->assertTrue(preg_match($bad_pattern, $c) == 0, 'Found ' . $bad_pattern . ' in ' . $path);
    }

    protected function is_forum_table($table)
    {
        if (in_array($table, [
            'group_privileges', 'member_privileges', 'group_category_access', 'member_category_access',
            'attachments', 'attachment_refs',
            'notifications_enabled',
            'translate',
            'member_tracking',
            'custom_comcode',
        ])) {
            return null;
        }

        return (substr($table, 0, 2) == 'f_') && ($table != 'f_welcome_emails');
    }
}
