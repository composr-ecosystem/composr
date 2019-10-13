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
class cloudflare_ip_range_sync_test_set extends cms_test_case
{
    public function testInSync()
    {
        $current = trim(http_get_contents('https://www.cloudflare.com/ips-v4')) . "\n" . trim(http_get_contents('https://www.cloudflare.com/ips-v6')); // TODO #3467

        $c = cms_file_get_contents_safe(get_file_base() . '/sources/global.php');
        $matches = array();
        preg_match('#\$trusted_proxies = \'([^\']*)\';#', $c, $matches);
        $in_code = str_replace(',', "\n", $matches[1]);

        $this->assertTrue($in_code == $current, 'Expected ' . $current . ', got ' . $in_code);
    }
}
