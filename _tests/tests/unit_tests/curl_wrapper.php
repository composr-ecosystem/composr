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
class curl_wrapper_test_set extends cms_test_case
{
    public function testCURLWrapper()
    {
        if (!addon_installed('sugarcrm')) {
            $this->assertTrue(false, 'The sugarcrm addon must be installed for this test to run');
            return;
        }

        if (!function_exists('mb_strtoupper')) {
            $this->assertTrue(false, 'mbstring needed');
            return;
        }

        require_code('curl');
        $ob = new Alexsoft\Curl();
        $result = $ob->get('https://www.example.com');
        $this->assertTrue((strpos($result['body'], 'Example Domain') !== false), 'Expected the body to contain Example Domain, but it did not.');
        $this->assertTrue(((trim($result['statusCode']) == '200') || (trim($result['statusCode']) == '200 OK')), 'Expected response code to be 200, but instead it was ' . $result['statusCode']);
    }
}
