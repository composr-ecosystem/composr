<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// php _tests/index.php __backups

/**
 * Composr test case class (unit testing).
 */
class __backups_test_set extends cms_test_case
{
    public function testBackup()
    {
        if (get_db_type() == 'xml') {
            warn_exit('Cannot run on XML database driver');
        }

        require_lang('backups');
        require_code('backup');
        require_code('tar');
        require_code('files');

        disable_php_memory_limit();

        $temp_test_dir = 'exports/backups/test';
        $temp_test_dir_full = get_custom_file_base() . '/' . $temp_test_dir;

        if (!$this->debug) {
            cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
            
            set_option('backup_server_hostname', '');
            $backup_name = 'test_backup';
            $backup_tar_path = get_custom_file_base() . '/exports/backups/' . $backup_name . '.tar';
            @unlink($backup_tar_path);
            make_backup($backup_name);
            $success = is_file($backup_tar_path);
            $this->assertTrue($success, 'Backup failed to generate');
            if (!$success) {
                return;
            }

            $resource = tar_open($backup_tar_path, 'rb');
            deldir_contents($temp_test_dir);
            @mkdir($temp_test_dir_full, 0777);
            tar_extract_to_folder($resource, $temp_test_dir);
            tar_close($resource);
            $success = is_file($temp_test_dir_full . '/restore.php');
            $this->assertTrue($success, 'Backup did not extract as expected (1)');
            if (!$success) {
                return;
            }
            $success = is_file($temp_test_dir_full . '/restore_data.php');
            $this->assertTrue($success, 'Backup did not extract as expected (2)');
            if (!$success) {
                return;
            }
        }

        if (get_file_base() != get_custom_file_base()) {
            $this->assertTrue(false, 'Test cannot run further, as a backup would only contain custom data and thus not run standalone');
            return;
        }

        $username = ((!isset($SITE_INFO['mysql_root_password'])) || (strpos(get_db_type(), 'mysql') === false)) ? get_db_site_user() : 'root';
        $can_use_own_db = ($username == 'root');
        $database = ($can_use_own_db) ? 'cms_backup_test' : get_db_site();
        $table_prefix = ($can_use_own_db) ? 'backup_' : 'bt' . $GLOBALS['SITE_DB']->get_table_prefix();
        $password = (isset($SITE_INFO['mysql_root_password'])) ? $SITE_INFO['mysql_root_password'] : get_db_site_password();

        require_code('database/' . get_db_type());
        $db_driver = object_factory('Database_Static_' . get_db_type(), false, [$table_prefix]);

        if ($can_use_own_db) {
            $db = new DatabaseConnector(get_db_site(), get_db_site_host(), $username, $password, $table_prefix, false, $db_driver); // Use site DB for actual connection because our test DB might not yet exist
            $db->query('CREATE DATABASE IF NOT EXISTS ' . $database, null, 0, true); // Suppress errors as the database might already exist
            unset($db);
        }

        global $SITE_INFO;
        $config_path = get_custom_file_base() . '/' . $temp_test_dir . '/_config.php';
        $config_php = cms_file_get_contents_safe($config_path, FILE_READ_LOCK);
        $config_php .= rtrim('
unset($SITE_INFO[\'base_url\']); // Let it auto-detect
unset($SITE_INFO[\'cns_table_prefix\']);
unset($SITE_INFO[\'db_forums\']);
unset($SITE_INFO[\'db_forums_user\']);
unset($SITE_INFO[\'db_forums_password\']);
$SITE_INFO[\'db_site\'] = \'' . $database . '\';
$SITE_INFO[\'db_site_user\'] = \'' . $username . '\';
$SITE_INFO[\'db_site_password\'] = \'' . $password . '\';
$SITE_INFO[\'table_prefix\'] = \'' . $table_prefix . '\';
$SITE_INFO[\'multi_lang_content\'] = \'' . addslashes($SITE_INFO['multi_lang_content']) . '\';
        ') . "\n";
        cms_file_put_contents_safe($config_path, $config_php, FILE_WRITE_FAILURE_CRITICAL);

        if (!$this->debug) { // We assume the backup test already ran if debug is present
            for ($i = 0; $i < 2; $i++) {
                cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
                $test = cms_http_request(get_custom_base_url() . '/exports/backups/test/restore.php?time_limit=1000', ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'trigger_error' => false, 'post_params' => [], 'timeout' => 1000.0]);
                $success = ($test->data !== null) && (strpos($test->data, do_lang('backups:BACKUP_RESTORE_SUCCESS')) !== false);
                $message = 'Failed to run restorer script on iteration ' . strval($i + 1) . ' [' . $test->data . ']; to debug manually run exports/backups/test/restore.php?time_limit=1000 . After completing the restore, run this test again with debug=1.';
                if (strpos(get_db_type(), 'odbc') !== false) {
                    $message .= '. Ensure that an ODBC connection has been added for cms_backup_test (we have auto-created the database itself)';
                }
                $this->assertTrue($success, $message);
                if (!$success) {
                    return;
                }
            }
        }

        // Now determine errors in expected row counts
        $db = new DatabaseConnector($database, get_db_site_host(), $username, $password, $table_prefix, false, $db_driver);

        $has_db_meta = $db->query_select_value_if_there('db_meta', 'COUNT(*)');
        if ($has_db_meta === null) {
            $this->assertTrue(false, 'Failed to restore database; db_meta is missing');
            return;
        }

        $has_db_meta_indices = $db->query_select_value_if_there('db_meta_indices', 'COUNT(*)');
        if ($has_db_meta_indices === null) {
            $this->assertTrue(false, 'Failed to restore database; db_meta_indices is missing');
            return;
        }

        require_code('database_relations');

        $tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table AS m_table']);
        foreach ($tables as $_table) {
            $table = $_table['m_table'];
            if (table_has_purpose_flag($table, TABLE_PURPOSE__NO_BACKUPS)) {
                continue;
            }

            $_db = get_db_for($table);

            $count_a = $_db->query_select_value($table, 'COUNT(*)');
            $count_b = $db->query_select_value_if_there($table, 'COUNT(*)');

            if ($count_b === null) {
                $this->assertTrue(false, 'Failed to restore table ' . $table);
            } else {
                $this->assertTrue(($count_a == $count_b), 'Expected ' . $count_a . ' rows to be restored from ' . $table . ' but instead got ' . $count_b);
            }
        }

        deldir_contents($temp_test_dir_full);
    }
}
