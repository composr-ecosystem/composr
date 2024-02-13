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

// php _tests/index.php __installer

/*
Note that this test installs Composr to a new database ON TOP your dev install, using a new _config.php file.
The test installs using the root MySQL user, and whatever is defined in your $SITE_INFO['mysql_root_password'] (or blank).
Your _config.php file is backed up to _config.php.bak in case the test fails and leaves you with a broken install.
If the test fails, make sure to manually revert _config.php before re-running it.
*/

/**
 * Composr test case class (unit testing).
 */
class __installer_test_set extends cms_test_case
{
    public function testQuickInstallerBuildsAndDoesNotFullyCrash()
    {
        if (($this->only !== null) && ($this->only != 'testQuickInstallerBuildsAndDoesNotFullyCrash')) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Cannot run test without MySQL');
            return;
        }

        $_GET['skip_quick'] = '0';
        $_GET['skip_manual'] = '0';
        $_GET['skip_bundled'] = '0';
        $_GET['skip_mszip'] = '0';

        require_code('version2');
        require_code('make_release');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $builds_path = get_builds_path();
        $version_dotted = get_version_dotted();
        $version_branch = get_version_branch();
        $build_path = $builds_path . '/builds/build/' . $version_branch;
        $installer_path = $builds_path . '/builds/' . $version_dotted . '/install.php';

        $url = get_custom_base_url() . '/exports/builds/' . $version_dotted . '/install.php';

        if ((!is_file($installer_path)) || ($this->only == 'testQuickInstallerBuildsAndDoesNotFullyCrash')) {
            make_installers();
        }

        $this->assertTrue(file_exists($build_path . '/site/index.php'), 'Could not find ' . $build_path . '/site/index.php');
        $this->assertTrue(file_exists($build_path . '/docs/LICENSE.md'), 'Could not find ' . $build_path . '/docs/LICENSE.md');
        $this->assertTrue(!file_exists($build_path . '/docs/index.php'), 'Could not find ' . $build_path . '/docs/index.php');

        if (get_param_integer('build_only', 0) != 1) {
            $http_result = cms_http_request($url, ['convert_to_internal_encoding' => true, 'timeout' => 60.0]);

            $this->assertTrue(($http_result->message == '200'), 'Error testing install.php in exports/builds (' . $http_result->message . ')');
        }
    }

    public function testDoesNotFullyCrash()
    {
        if (($this->only !== null) && ($this->only != 'testDoesNotFullyCrash')) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Cannot run test without MySQL');
            return;
        }

        $http_result = cms_http_request(get_base_url() . '/install.php?skip_slow_checks=1', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 60.0]);

        $this->assertTrue($http_result->message == '200', 'Wrong HTTP status code ' . $http_result->message);

        $success = (strpos($http_result->data, 'type="submit"') !== false);
        if ((!$success) && ($this->debug)) {
            @var_dump($http_result->data);
            exit();
        }
        $this->assertTrue($success, 'No submit button found'); // Has start button: meaning something worked
    }

    public function testFullInstallSafeMode()
    {
        if (($this->only !== null) && ($this->only != 'testFullInstallSafeMode')) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Cannot run test without MySQL');
            return;
        }

        $this->do_headless_install(true);
    }

    public function testFullInstallNormalMode()
    {
        if (($this->only !== null) && ($this->only != 'testFullInstallNormalMode')) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Cannot run test without MySQL');
            return;
        }

        $this->do_headless_install(false);
    }

    protected function do_headless_install($safe_mode)
    {
        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Cannot run test without MySQL');
            return false;
        }

        $database = 'cms__test';
        $table_prefix = 'installer_';

        // Cleanup old install
        $tables = $GLOBALS['SITE_DB']->query('SHOW TABLES FROM ' . $database, null, 0, true); // Suppress errors in case database does not exist yet
        if ($tables === null) {
            $tables = [];
        }
        foreach ($tables as $table) {
            if (substr($table['Tables_in_' . $database], 0, strlen($table_prefix)) == $table_prefix) {
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS ' . $database . '.' . $table['Tables_in_' . $database]);
            }
        }

        // Assumes we're using a blank root password, which is typically the case on development) - or you have it in $SITE_INFO['mysql_root_password']
        global $SITE_INFO;
        require_code('install_headless');
        for ($i = 0; $i < (($this->only === null) ? 2 : 1); $i++) { // 1st trial is clean DB, 2nd trial is dirty DB
            cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

            $success = do_install_to(
                $database,
                (strpos(get_db_type(), 'mysql') === false) ? get_db_site_user() : 'root',
                isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '',
                $table_prefix,
                $safe_mode,
                'cns',
                null,
                null,
                null,
                null,
                null,
                [],
                true,
                'mysqli'
            );
            $fail_message = 'Failed on trial #' . strval($i + 1) . ' ';
            $fail_message .= ($safe_mode ? '(safe mode)' : '(no safe mode)');
            if (!$this->debug) {
                $fail_message .= ' -- append &debug=1 to the URL to get debug output / pass debug CLI parameter';
            }
            $this->assertTrue($success, $fail_message);

            if (!$success) {
                return false; // Don't do further trials if there's an error
            }
        }

        return true;
    }
}
