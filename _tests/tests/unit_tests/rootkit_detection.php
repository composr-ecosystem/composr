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
class rootkit_detection_test_set extends cms_test_case
{
    public function testRootkitDetection()
    {
        require_code('crypt_master');
        if (!check_master_password('')) {
            return; // If we don't have a blank password test cannot work
        }

        $post_params = array(
            'password' => '',
            'db_host' => get_db_site_host(),
            'db_name' => get_db_site(),
            'db_prefix' => get_table_prefix(),
            'db_user' => get_db_site_user(),
            'db_password' => get_db_site_password(),
            'do_files' => '0',
        );
        $result = http_download_file(get_base_url() . '/rootkit_detection.php?type=go', null, true, false, 'Composr', $post_params);
        $this->assertTrue(strpos($result, 'Privileges:') !== false);
        $this->assertTrue(strpos($result, 'PHP Warning') === false);
        $this->assertTrue(strpos($result, 'PHP Error') === false);
        $this->assertTrue(strpos($result, 'PHP Notice') === false);
    }
}
