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
class firephp_test_set extends cms_test_case
{
    public function testFirePHP()
    {
        $this->establish_admin_session();

        $url = build_url(array('page' => '', 'keep_firephp' => 1, 'keep_su' => $this->get_canonical_username('test')), 'adminzone');

        $default_opts = array(
            'http' => array(
                'header' => 'Cookie: ' . get_session_cookie() . '=' . get_session_id() . "\r\n" . 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36 FirePHP/4Chrome' . "\r\n" . 'X-FirePHP-Version: 0.0.6',
            )
        );
        stream_context_set_default($default_opts);
        $headers = @get_headers($url->evaluate());

        if (!empty($_GET['debug'])) { // TODO: Change in v11
            @var_dump($headers);
        }

        $this->assertTrue($headers !== false, 'HTTP request failed');

        $found = false;
        if ($headers !== false) {
            foreach ($headers as $header) {
                $found = $found || (strpos($header, 'Permission check FAILED: has_zone_access: adminzone') !== false);
            }
        }
        $this->assertTrue($found, 'Did not find logged permission check failing');
    }
}
