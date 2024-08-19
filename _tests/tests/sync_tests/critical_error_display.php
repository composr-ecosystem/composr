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
class critical_error_display_test_set extends cms_test_case
{
    public function testCriticalErrorScreen()
    {
        $e_path = get_file_base() . '/_critical_error.html';
        file_put_contents($e_path, 'xxx123');

        $dir = get_custom_file_base() . '/critical_errors';
        @mkdir($dir, 0777);

        $c_path = get_file_base() . '/_config.php';
        rename($c_path, $c_path . '.old'); // Rename config file to intentionally break Composr
        $result = cms_http_request(get_base_url() . '/index.php', ['convert_to_internal_encoding' => true, 'timeout' => 10.0]);
        $this->assertTrue(strpos($result->download_url, '_critical_error.html') !== false, 'Got ' . $result->download_url . ' (' . serialize($result) . ')');
        rename($c_path . '.old', $c_path);

        unlink($e_path);

        require_code('files');
        deldir_contents($dir);
    }
}
