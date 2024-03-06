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

// php _tests/index.php __installer_xml_db

/*
Note that this test installs Composr to a new database ON TOP your dev install, using a new _config.php file.
The test installs using the root MySQL user, and whatever is defined in your $SITE_INFO['mysql_root_password'] (or blank).
Your _config.php file is backed up to _config.php.bak in case the test fails and leaves you with a broken install.
If the test fails, make sure to manually revert _config.php before re-running it.
*/

/**
 * Composr test case class (unit testing).
 */
class __installer_xml_db_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (!is_cli()) {
            warn_exit('This test should be run on the command line: php _tests/index.php __installer_xml_db.');
        }
    }

    public function testFullInstallSafeMode()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $database = 'cms_test';
        $table_prefix = 'xmldb_';

        deldir_contents(get_custom_file_base() . '/uploads/website_specific/' . $database);

        global $SITE_INFO;
        require_code('install_headless');
        for ($i = 0; $i < 2; $i++) { // 1st trial is clean DB, 2nd trial is dirty DB
            $success = do_install_to($database, 'root', '', $table_prefix, false, 'cns', null, null, null, null, null, [], true, 'xml');
            $this->assertTrue($success);

            if (!$success) {
                break; // Don't do further trials if there's an error
            }
        }
    }
}
