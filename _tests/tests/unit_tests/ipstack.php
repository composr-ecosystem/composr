<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class ipstack_test_set extends cms_test_case
{
    public function testIpStack()
    {
        $key = '61b2798296ac2f610584042dae5fc813';
        $ip_stack_url = 'http://api.ipstack.com/' . rawurlencode('8.8.8.8') . '?access_key=' . urlencode($key);
        $_json = http_get_contents($ip_stack_url, array('trigger_error' => false)); // TODO #3467
        $json = json_decode($_json, true);
        $this->assertTrue($json['country_name'] == 'United States');
    }
}
