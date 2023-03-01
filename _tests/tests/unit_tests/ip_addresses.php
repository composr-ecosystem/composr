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
class ip_addresses_test_set extends cms_test_case
{
    public function testCIDR()
    {
        $this->assertTrue(ip_cidr_check('204.93.240.1', '204.93.240.0/24'));
        $this->assertTrue(!ip_cidr_check('204.93.241.1', '204.93.240.0/24'));
    }

    public function testFuncs()
    {
        require_code('failure');

        $this->assertTrue(!is_valid_ip('*.168.1.1'));
        $this->assertTrue(is_valid_ip('192.168.1.1', true));
        $this->assertTrue(!is_valid_ip('x.168.1.1', true));
        $this->assertTrue(is_valid_ip('192.168.1.*', true));
        $this->assertTrue(!is_valid_ip('*.168.1.1', true));
        $this->assertTrue(!is_valid_ip('*:db8::a00:20ff:fea7:ccea'));
        $this->assertTrue(is_valid_ip('2001:db8::a00:20ff:fea7:ccea', true));
        $this->assertTrue(!is_valid_ip('x:db8::a00:20ff:fea7:ccea', true));
        $this->assertTrue(!is_valid_ip('*:db8::a00:20ff:fea7:ccea', true));
        $this->assertTrue(is_valid_ip('db8::a00:20ff:fea7:ccea:*', true));

        $this->assertTrue(normalise_ip_address('192.168.1.1') == '192.168.1.1');
        $this->assertTrue(normalise_ip_address('192.168.1') == '192.168.1.0');
        $this->assertTrue(normalise_ip_address('192.168.1000') == ''); // Cannot normalise
        $this->assertTrue(normalise_ip_address('2001:db8::a00:20ff:fea7:ccea') == '2001:0DB8:0000:0000:0A00:20FF:FEA7:CCEA');
        $this->assertTrue(normalise_ip_address('2001:db8::20ff:fea7:ccea') == '2001:0DB8:0000:0000:0000:20FF:FEA7:CCEA');
        $this->assertTrue(normalise_ip_address('::1') == '0000:0000:0000:0000:0000:0000:0000:0001');

        $this->assertTrue(ip_wild_to_apache('192.168.1.1') == '192.168.1.1');
        $this->assertTrue(ip_wild_to_apache('2001:db8::a00:20ff:fea7:ccea') == '2001:0DB8:0000:0000:0A00:20FF:FEA7:CCEA');
        $this->assertTrue(ip_wild_to_apache('192.168.1.*') == '192.168.1.0/24');
        $this->assertTrue(ip_wild_to_apache('*.168.1.1') == ''); // Considered invalid, * must be on end
        $this->assertTrue(ip_wild_to_apache('f:db8::a00:20ff:fea7:*') == '000F:0DB8:0000:0000:0A00:20FF:FEA7:0000/112');
        $this->assertTrue(ip_wild_to_apache('*:f:db8::a00:20ff:fea7:') == ''); // Considered invalid, * must be on end

        $this->assertTrue(compare_ip_address('192.168.1.1', '192.168.1.1'));
        $this->assertTrue(!compare_ip_address('192.168.1.1', '192.168.1.2'));
        $this->assertTrue(compare_ip_address('2001:db8::a00:20ff:fea7:ccea', '2001:db8::a00:20ff:fea7:ccea'));
        $this->assertTrue(!compare_ip_address('2001:db8::a00:20ff:fea7:ccea', '2001:db8::a00:20ff:fea7:cceb'));
        $this->assertTrue(compare_ip_address('192.168.1.*', '192.168.1.1'));
        $this->assertTrue(!compare_ip_address('192.168.1.*', '192.168.2.1'));
        $this->assertTrue(compare_ip_address('192.168.1.*', '192.168.1.1'));
        $this->assertTrue(!compare_ip_address('192.168.1.*', '192.168.2.1'));
        $this->assertTrue(compare_ip_address('db8::a00:20ff:fea7:ccea:*', 'db8::a00:20ff:fea7:ccea:0000'));
        $this->assertTrue(!compare_ip_address('db8::a00:20ff:fea7:ccea:*', 'db8::a00:20ff:fea7:cceb:0000'));
    }

    public function testIPAddressSanitisation()
    {
        $expectations = [
            '' => false,

            'x' => false,

            '127.0.0.1' => true,
            '255.255.255.255' => true,
            '255.255.255.255.255' => false,
            '0.0.0.0' => true,
            '192.168.1' => false,
            '-0.0.0.0' => false,
            '-1.0.0.0' => false,
            '0.0.0.' => false,
            '.0.0.0' => false,
            ' 0.0.0.0' => false,
            '0.0.0.0 ' => false,
            '0.0.0 .0' => false,
            '0.0.0' => false,
            '0.0.0.0.0' => false,
            '256.256.256.256' => false,
            '1111.1111.1111.1111' => false,
            'a.a.a.a' => false,

            'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF' => true,
            'FFFF::FFFF:FFFF:FFFF:FFFF' => true, // Double colon allows shortening
            'FFFF::FFFF:FFFF::FFFF' => false, // Only 1 double colon allowed
            'FFFF::FFFF:1:FFFF:FFFF' => true,
            'A:0:1:2:3:4:5:6' => true, // Leading zeroes can be omitted
            '0000:0000:0000:0000:0000:0000:0000:0000' => true,
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
            'gggg:gggg:gggg:gggg:gggg:gggg:gggg:gggg' => false,
            'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF' => false,
            'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF' => false, // Too short for when no double colons
        ];

        foreach ($expectations as $string => $status) {
            $this->assertTrue(is_valid_ip($string) == $status, 'Incorrect IP address status for ' . $string);
        }
    }
}
