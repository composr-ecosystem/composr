<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class closed_file_test_set extends cms_test_case
{
    public function testClosedFile()
    {
        $path = get_file_base() . '/closed.html';
        $test = 'Test';
        file_put_contents($path, $test);
        sync_file($path);

        $url = static_evaluate_tempcode(build_url(['page' => ''], ''));
        $result = cms_http_request($url);

        $this->assertTrue($result->download_url == get_base_url() . '/closed.html');

        unlink($path);
        sync_file($path);
    }
}
