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

// php _tests/index.php cli_tests/_installer_forum_drivers

/*
Note that this test installs Composr to a new database ON TOP your dev install, using a new _config.php file.
It assumes phpBB is in ../forums/phpBB3 relative to your base directory and there is a db.sql file in there which is a DB dump for it.
The test installs using the root MySQL user, and whatever is defined in your $SITE_INFO['mysql_root_password'] (or blank).
Your _config.php file is backed up to _config.php.bak in case the test fails and leaves you with a broken install.
If the test fails, make sure to manually revert _config.php before re-running it.
*/

/**
 * Composr test case class (unit testing).
 */
class _installer_forum_drivers_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (!is_cli()) {
            warn_exit('This test should be run on the command line: php _tests/index.php cli_tests/_installer_forum_drivers');
        }
    }

    public function testPhpBBInstall()
    {
        if (($this->only !== null) && ($this->only != 'testPhpBBInstall')) {
            return;
        }

        global $SITE_INFO;
        $username = 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $board_path = dirname(get_file_base()) . '/forums/phpBB3';
        if (!file_exists($board_path)) {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . ' is not there. This test makes some implicit assumptions, check the code to see');
            return;
        }
        if (!file_exists($board_path . '/db.sql')) {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . '/db.sql is not there');
            return;
        }
        $forum_base_url = dirname(get_base_url()) . '/forums/phpBB3';
        $database_forums = 'forum_phpbb_31';
        $extra_settings = [
            'phpbb_table_prefix' => 'phpbb_',
            'use_multi_db' => '1',
        ];
        $cmd = 'mysql -uroot';
        if ($password != '') {
            $cmd .= ' -p' . $password;
        }
        $cmd .= ' ' . $database_forums . ' < ' . $board_path . '/db.sql';
        shell_exec($cmd);

        $this->do_headless_install(false, 'phpbb3', $username, $password, $board_path, $forum_base_url, $database_forums, null, null, $extra_settings);
    }

    public function testNoneInstall()
    {
        if (($this->only !== null) && ($this->only != 'testNoneInstall')) {
            return;
        }

        global $SITE_INFO;
        $username = (strpos(get_db_type(), 'mysql') === false) ? get_db_site_user() : 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $this->do_headless_install(false, 'none', $username, $password);
    }

    protected function do_headless_install($safe_mode = false, $forum_driver = 'cns', $username = null, $password = null, $board_path = null, $forum_base_url = null, $database_forums = null, $username_forums = null, $password_forums = null, $extra_settings = [])
    {
        global $SITE_INFO;

        $username = ((!isset($SITE_INFO['mysql_root_password'])) || (strpos(get_db_type(), 'mysql') === false)) ? get_db_site_user() : 'root';
        $can_use_own_db = ($username == 'root');
        $database = ($can_use_own_db) ? 'cms__forumtest' : get_db_site();
        $table_prefix = (($can_use_own_db) ? 'installer_' : ('fdt' . $GLOBALS['SITE_DB']->get_table_prefix()));
        $password = (isset($SITE_INFO['mysql_root_password'])) ? $SITE_INFO['mysql_root_password'] : get_db_site_password();

        if ($can_use_own_db) {
            require_code('database/' . get_db_type());
            $db_driver = object_factory('Database_Static_' . get_db_type(), false, [$table_prefix]);
            $db = new DatabaseConnector(get_db_site(), get_db_site_host(), $username, $password, $table_prefix, false, $db_driver); // Use site DB for actual connection because our test DB might not yet exist
            $db->query('CREATE DATABASE IF NOT EXISTS ' . $database, null, 0, true); // Suppress errors as the database might already exist
        } else {
            $db = $GLOBALS['SITE_DB'];
        }

        // Assumes we're using a blank root password, which is typically the case on development) - or you have it in $SITE_INFO['mysql_root_password']
        require_code('install_headless');
        $success = do_install_to(
            $database,
            $username,
            $password,
            $table_prefix,
            $safe_mode,
            $forum_driver,
            $board_path,
            $forum_base_url,
            $database_forums,
            $username_forums,
            $password_forums,
            $extra_settings,
            true,
            'mysqli'
        );
        $this->assertTrue($success);

        // Cleanup old install
        $tables = $db->query('SHOW TABLES FROM ' . $database, null, 0);
        if ($tables === null) {
            $tables = [];
        }
        foreach ($tables as $table) {
            if (substr($table['Tables_in_' . $database], 0, strlen($table_prefix)) == $table_prefix) {
                $db->query('DROP TABLE IF EXISTS ' . $database . '.' . $table['Tables_in_' . $database]);
            }
        }
    }
}
