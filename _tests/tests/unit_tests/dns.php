<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class dns_test_set extends cms_test_case
{
    public function testGetHostByAddr()
    {
        set_value('slow_php_dns', '1');
        $host = cms_gethostbyaddr('8.8.8.8');
        $this->assertTrue($host == 'dns.google', 'Got ' . $host);

        set_value('slow_php_dns', '0');
        $host = cms_gethostbyaddr('8.8.8.8');
        $this->assertTrue($host == 'dns.google', 'Got ' . $host);
    }

    public function testGetHostByName()
    {
        set_value('slow_php_dns', '1');
        $host = cms_gethostbyname('dns.google');
        $this->assertTrue((substr($host, 0, 4) == '8.8.') || (strpos($host, ':') !== false), 'Got ' . $host);

        set_value('slow_php_dns', '0');
        $host = cms_gethostbyname('dns.google');
        $this->assertTrue(substr($host, 0, 4) == '8.8.', 'Got ' . $host);
    }
}
