<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class type_sanitisation_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('type_sanitisation');
    }

    public function testEmailAddressSanitisation()
    {
        // Some reasonable checks only, as e-mail address validation is incredibly complex...

        $expectations = [
            '' => false,

            'foo@example.com' => true,
            'foo+bar@example.com' => true,
            'foo@' => false,
            'foo' => false,
            '@example.com' => false,
            'example.com' => false,
            ' foo@example.com' => false,
            'foo@example.com ' => false,
            'foo @example.com' => false,
            'foo@ example.com' => false,
            'foo@example .com' => false,
            'foo@example' => true,
            'foo@@example' => false,
            'foo@example@' => false,
            '@foo@example' => false,
            'foo@ex$ample' => false,
            'foo@ex1ample' => true,
            'foo@ex,ample' => false,
            'foo@example,' => false,
            'foo,@example' => false,
            '_@127.0.0.1' => true,
            'a.b@127.0.0.1' => true,

            // Our regexp not smart enough for these but we do not care enough
            //'..@127.0.0.1' => false,
            //'foo@127.0.0.' => false,
        ];

        foreach ($expectations as $string => $status) {
            $this->assertTrue(is_email_address($string) == $status, 'Incorrect e-mail address status for ' . $string);
        }
    }

    public function testIPAddressSanitisation()
    {
        $expectations = [
            '' => false,

            '127.0.0.1' => true,
            '255.255.255.255' => true,
            '0.0.0.0' => true,
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
            'FFFF::FFFF:FFFF:FFFF:FFFF' => true,
            'FFFF::FFFF:1:FFFF:FFFF' => true,
            '0000:0000:0000:0000:0000:0000:0000:0000' => true,
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
            'gggg:gggg:gggg:gggg:gggg:gggg:gggg:gggg' => false,
            'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF' => false,
        ];

        foreach ($expectations as $string => $status) {
            $this->assertTrue(is_ip_address($string) == $status, 'Incorrect IP address status for ' . $string);
        }
    }
}
