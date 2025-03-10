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
class firephp_test_set extends cms_test_case
{
    public function testFirePHP()
    {
        $message = 'This test will likely fail if using a proxy such as Cloudflare or running on a host that modifies headers.';
        $this->dump($message, 'INFO:');

        $session_id = $this->establish_admin_callback_session();

        $url = build_url(['page' => '', 'keep_firephp' => 1, 'keep_su' => $this->get_canonical_username('test')], 'adminzone');

        $extra_headers = [
            'X-FirePHP-Version' => '0.0.6',
        ];
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36 FirePHP/4Chrome';
        $http_result = cms_http_request($url->evaluate(), ['ignore_http_status' => true, 'trigger_error' => false, 'ua' => $ua, 'extra_headers' => $extra_headers, 'cookies' => [get_session_cookie() => $session_id]]);

        $found = false;
        foreach ($http_result->headers as $header) {
            $found = $found || (strpos($header, 'Permission check FAILED: has_zone_access: adminzone') !== false);
        }
        $this->assertTrue($found, 'Could not find a firephp header');
        if ($this->debug) {
            $this->dump($http_result, 'HTTP result');
        }
    }
}
