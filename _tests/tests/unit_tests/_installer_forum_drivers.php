<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class _installer_forum_drivers_test_set extends cms_test_case
{
    public function testPhpBBInstall()
    {
        global $SITE_INFO;
        $username = 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $board_path = dirname(get_file_base()) . '/phpBB3';
        if (!file_exists($board_path))
        {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . ' is not there. This test makes some implicit assumptions, check the code to see');
            return;
        }
        if (!file_exists($board_path . '/db.sql'))
        {
            $this->assertTrue(false, 'Cannot run test, ' . $board_path . '/db.sql is not there');
            return;
        }
        $board_prefix = dirname(get_base_url()) . '/phpBB3';
        $database_forums = 'forum_phpbb_31';
        $extra_settings = array(
            'phpbb_table_prefix' => 'phpbb_',
            'use_multi_db' => '1',
        );
        $cmd = 'mysql -uroot';
        if ($password != '') {
            $cmd .= ' -p' . $password;
        }
        $cmd .= ' ' . $database_forums . ' < ' . $board_path . '/db.sql';
        shell_exec($cmd);

        $this->doHeadlessInstall(false, 'phpbb3', $username, $password, $board_path, $board_prefix, $database_forums, null, null, $extra_settings);
    }

    public function testNoneInstall()
    {
        global $SITE_INFO;
        $username = 'root';
        $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';

        $this->doHeadlessInstall(false, 'none', $username, $password);
    }

    private function doHeadlessInstall($safe_mode = false, $forum_driver = 'cns', $username = null, $password = null, $board_path = null, $board_prefix = null, $database_forums = null, $username_forums = null, $password_forums = null, $extra_settings = null)
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
            $board_prefix,
            $database_forums,
            $username_forums,
            $password_forums,
            $extra_settings
        );
        $this->assertTrue($success);
    }
}
