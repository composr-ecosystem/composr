<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php __installer_xml_db

/**
 * Composr test case class (unit testing).
 */
class __installer_xml_db_test_set extends cms_test_case
{
    public function testFullInstallSafeMode()
    {
        $database = 'test';
        $table_prefix = 'cms_xmldb_test_';

        deldir_contents(get_file_base(true) . '/uploads/website_specific/' . $database);

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
