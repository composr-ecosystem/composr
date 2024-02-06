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

/**
 * Composr test case class (unit testing).
 */
class rootkit_detection_test_set extends cms_test_case
{
    public function testRootkitDetection()
    {
        $password = '';
        global $SITE_INFO;
        if ((!empty($SITE_INFO['master_password'])) && (strlen($SITE_INFO['master_password']) != 32) && (strlen($SITE_INFO['master_password']) != 60)) {
            $password = $SITE_INFO['master_password'];
        }

        require_code('crypt_master');
        if (!check_master_password($password)) {
            $this->assertTrue(false, 'Cannot run test unless admin password is blank or defined as non-hashed');
            return; // If we don't have a blank password test cannot work
        }

        $post_params = [
            'password' => $password,
            'db_host' => get_db_site_host(),
            'db_name' => get_db_site(),
            'db_prefix' => get_table_prefix(),
            'db_user' => get_db_site_user(),
            'db_password' => get_db_site_password(),
            'do_files' => '0',
        ];
        $result = http_get_contents(get_base_url() . '/rootkit_detection.php?type=go', ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'post_params' => $post_params]);
        $this->assertTrue(strpos($result, 'Privileges:') !== false, 'Failed to see listed privileges');
        $this->assertTrue(strpos($result, 'Fatal error') === false, 'Found a fatal error');
        $this->assertTrue(strpos($result, 'PHP Error') === false, 'Found an error');
        $this->assertTrue(strpos($result, 'PHP Warning') === false, 'Found a warning');
        $this->assertTrue(strpos($result, 'PHP Notice') === false, 'Found a notice');
    }
}
