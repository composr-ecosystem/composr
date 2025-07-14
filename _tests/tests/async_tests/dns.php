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
        $this->assertTrue((substr($host, 0, 4) == '8.8.') || (substr($host, 0, 14) == '2001:4860:4860'), 'Got ' . $host);

        set_value('slow_php_dns', '0');
        $host = cms_gethostbyname('dns.google');
        $this->assertTrue((substr($host, 0, 4) == '8.8.') || (substr($host, 0, 14) == '2001:4860:4860'), 'Got ' . $host);
    }
}
