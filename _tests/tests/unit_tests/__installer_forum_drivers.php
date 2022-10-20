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

// php _tests/index.php __installer_forum_drivers

/*
Note that this test installs Composr to a new database ON TOP your dev install, using a new _config.php file.
The test installs using the root MySQL user, and whatever is defined in your $SITE_INFO['mysql_root_password'] (or blank).
Your _config.php file is backed up to _config.php.bak in case the test fails and leaves you with a broken install.
If the test fails, make sure to manually revert _config.php before re-running it.
*/

/**
 * Composr test case class (unit testing).
 */
class __installer_forum_drivers_test_set extends cms_test_case
{
    public function testPhpBBInstall()
    {
        if (($this->only !== null) && ($this->only != 'testPhpBBInstall')) {
            return;
        }

        global $SITE_INFO;
        $username = 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $board_path = dirname(get_file_base()) . '/phpBB3';
        if (!file_exists($board_path)) {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . ' is not there. This test makes some implicit assumptions, check the code to see');
            return;
        }
        if (!file_exists($board_path . '/db.sql')) {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . '/db.sql is not there');
            return;
        }
        $forum_base_url = dirname(get_base_url()) . '/phpBB3';
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
        $database = 'test';
        $table_prefix = 'cms_forumdriver_test_';

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
    }
}
