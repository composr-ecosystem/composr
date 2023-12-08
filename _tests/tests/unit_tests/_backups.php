<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _backups_test_set extends cms_test_case
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
        $temp_test_dir = 'exports/backups/test';
        $temp_test_dir_full = get_custom_file_base() . '/' . $temp_test_dir;
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

        global $SITE_INFO;
        $config_path = get_custom_file_base() . '/' . $temp_test_dir . '/_config.php';
        $config_php = file_get_contents($config_path);
        $config_php .= rtrim('
unset($SITE_INFO[\'base_url\']); // Let it auto-detect
unset($SITE_INFO[\'cns_table_prefix\']);
$SITE_INFO[\'db_site\'] = \'cms_backup_test\';
$SITE_INFO[\'db_forums\'] = \'cms_backup_test\';
$SITE_INFO[\'table_prefix\'] = \'cms_backup_test_\';
$SITE_INFO[\'multi_lang_content\'] = \'' . addslashes($SITE_INFO['multi_lang_content']) . '\';
        ');
        cms_file_put_contents_safe($config_path, $config_php);

        $GLOBALS['SITE_DB']->query('CREATE DATABASE cms_backup_test', null, null, true);

        for ($i = 0; $i < 2; $i++) {
            $test = http_download_file(get_base_url() . '/exports/backups/test/restore.php?time_limit=1000', null, false, false, 'Composr', array(), null, null, null, null, null, null, null, 1000.0);
            $success = (strpos($test, do_lang('backups:BACKUP_RESTORE_SUCCESS')) !== false);
            $message = 'Failed to run restorer script on iteration ' . strval($i + 1) . ' [' . $test . ']; to debug manually run exports/backups/test/restore.php?time_limit=1000';
            if (strpos(get_db_type(), 'odbc') !== false) {
                $message .= '. Ensure that an ODBC connection has been added for cms_backup_test (we have auto-created the database itself)';
            }
            $this->assertTrue($success, $message);
            if (!$success) {
                return;
            }
        }

        $db = new DatabaseConnector('cms_backup_test', get_db_site_host(), get_db_site_user(), get_db_site_password(), 'cms_backup_test_');
        $count = $db->query_select_value('zones', 'COUNT(*)');
        $this->assertTrue($count > 0, 'Failed to restore database');

        deldir_contents($temp_test_dir_full);
    }
}
