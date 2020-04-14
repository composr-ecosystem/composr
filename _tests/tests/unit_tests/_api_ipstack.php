<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _api_ipstack_test_set extends cms_test_case
{
    public function testIpStack()
    {
        $this->load_key_options('ipstack');

        // NB: https requires a paid plan
        $ip_stack_url = 'http://api.ipstack.com/' . rawurlencode('8.8.8.8') . '?access_key=' . urlencode(get_option('ipstack_api_key'));
        $_json = http_get_contents($ip_stack_url, ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0]);
        $json = json_decode($_json, true);
        $this->assertTrue($json['country_name'] == 'United States');
    }
}
