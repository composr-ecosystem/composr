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

        global $SITE_INFO;
        $config_path = get_custom_file_base() . '/' . $temp_test_dir . '/_config.php';
        $config_php = cms_file_get_contents_safe($config_path, FILE_READ_LOCK);
        $config_php .= rtrim('
unset($SITE_INFO[\'base_url\']); // Let it auto-detect
unset($SITE_INFO[\'cns_table_prefix\']);
unset($SITE_INFO[\'db_forums\']);
unset($SITE_INFO[\'db_forums_user\']);
unset($SITE_INFO[\'db_forums_password\']);
$SITE_INFO[\'db_site\'] = \'cms_backup_test\';
if ((isset($SITE_INFO[\'db_type\'])) && (strpos($SITE_INFO[\'db_type\'], \'mysql\') !== false)) {
    $SITE_INFO[\'db_site_user\'] = \'root\';
}
$SITE_INFO[\'db_site_password\'] = isset($SITE_INFO[\'mysql_root_password\']) ? $SITE_INFO[\'mysql_root_password\'] : \'\';
$SITE_INFO[\'table_prefix\'] = \'cms_backup_test_\';
$SITE_INFO[\'multi_lang_content\'] = \'' . addslashes($SITE_INFO['multi_lang_content']) . '\';
        ') . "\n";
        cms_file_put_contents_safe($config_path, $config_php, FILE_WRITE_FAILURE_CRITICAL);

        global $SITE_INFO;
        $username = (strpos(get_db_type(), 'mysql') === false) ? get_db_site_user() : 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $db = new DatabaseConnector(get_db_site(), get_db_site_host(), $username, $password, $GLOBALS['SITE_DB']->get_table_prefix());
        $db->query('CREATE DATABASE cms_backup_test', null, 0, true); // Suppress errors in case already exists
        unset($db);

        for ($i = 0; $i < 2; $i++) {
            $test = cms_http_request(get_custom_base_url() . '/exports/backups/test/restore.php?time_limit=1000', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'post_params' => [], 'timeout' => 1000.0]);
            $success = ($test->data !== null) && (strpos($test->data, do_lang('backups:BACKUP_RESTORE_SUCCESS')) !== false);
            $message = 'Failed to run restorer script on iteration ' . strval($i + 1) . ' [' . $test->data . ']; to debug manually run exports/backups/test/restore.php?time_limit=1000';
            if (strpos(get_db_type(), 'odbc') !== false) {
                $message .= '. Ensure that an ODBC connection has been added for cms_backup_test (we have auto-created the database itself)';
            }
            $this->assertTrue($success, $message);
            if (!$success) {
                return;
            }
        }

        // Now determine errors in expected row counts
        $db = new DatabaseConnector('cms_backup_test', get_db_site_host(), $username, $password, 'cms_backup_test_');
        
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
