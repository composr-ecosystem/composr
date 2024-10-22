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

/**
 * Composr test case class (unit testing).
 */
class rootkit_detection_test_set extends cms_test_case
{
    protected $config;
    protected $password;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        global $SITE_INFO;
        if ((!empty($SITE_INFO['maintenance_password'])) && (strlen($SITE_INFO['maintenance_password']) != 32) && (strlen($SITE_INFO['maintenance_password']) != 60)) {
            $this->password = $SITE_INFO['maintenance_password'];
        }

        $config_file_path = get_file_base() . '/_config.php';
        $this->config = cms_file_get_contents_safe($config_file_path, FILE_READ_LOCK);
        file_put_contents($config_file_path, $this->config . "\n\n\$SITE_INFO['maintenance_password'] = '';");
        fix_permissions($config_file_path);
    }

    public function testRootkitDetection()
    {
        $this->assertTrue(false, 'Run the rootkit detection manually at rootkit_detection.php; this test does not work.');
        return;

        /*
        require_code('crypt_maintenance');

        $post_params = [
            'password' => '', // Does not work when it should
            'db_host' => get_db_site_host(),
            'db_name' => get_db_site(),
            'db_prefix' => get_table_prefix(),
            'db_user' => get_db_site_user(),
            'db_password' => get_db_site_password(),
            'do_files' => '0',
        ];
        $result = http_get_contents(get_base_url() . '/rootkit_detection.php?type=go', ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'post_params' => $post_params]);
        $this->assertTrue(strpos($result, 'Denied') === false, 'Access denied');
        $this->assertTrue(strpos($result, 'Privileges:') !== false, 'Failed to see listed privileges');
        $this->assertTrue(strpos($result, 'Fatal error') === false, 'Found a fatal error');
        $this->assertTrue(strpos($result, 'PHP Error') === false, 'Found an error');
        $this->assertTrue(strpos($result, 'PHP Warning') === false, 'Found a warning');
        $this->assertTrue(strpos($result, 'PHP Notice') === false, 'Found a notice');

        if ($this->debug) {
            $this->dump($result, 'Rootkit Detection results');
        }
        */
    }

    public function tearDown()
    {
        $config_file_path = get_file_base() . '/_config.php';
        file_put_contents($config_file_path, $this->config);

        parent::tearDown();
    }
}
