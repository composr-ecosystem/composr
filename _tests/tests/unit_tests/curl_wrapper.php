<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class curl_wrapper_test_set extends cms_test_case
{
    public function testCURLWrapper()
    {
        if (!addon_installed('sugarcrm')) {
            $this->assertTrue(false, 'The sugarcrm addon must be installed for this test to run');
            return;
        }

        require_code('curl');
        $ob = new Alexsoft\Curl();
        $result = $ob->get('https://www.example.com');
        $this->assertTrue(strpos($result['body'], 'Example Domain') !== false);
        $this->assertTrue(trim($result['statusCode']) == '200');
    }
}
