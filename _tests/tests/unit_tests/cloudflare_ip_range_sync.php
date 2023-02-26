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
class cloudflare_ip_range_sync_test_set extends cms_test_case
{
    public function testInSync()
    {
        $current = '';
        $current .= trim(unixify_line_format(http_get_contents('https://www.cloudflare.com/ips-v4', ['convert_to_internal_encoding' => true])));
        $current .= "\n";
        $current .= trim(unixify_line_format(http_get_contents('https://www.cloudflare.com/ips-v6', ['convert_to_internal_encoding' => true])));
        $current = str_replace("\n", ',', $current);

        $c = cms_file_get_contents_safe(get_file_base() . '/sources/global.php', FILE_READ_LOCK);
        $matches = [];
        preg_match('#\$trusted_proxies = \'([^\']*)\';#', $c, $matches);
        $in_code = $matches[1];

        $this->assertTrue($in_code == $current, 'Expected ' . $current . ' in sources/global.php, got ' . $in_code);
    }
}
