<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class crypt_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('crypt');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }

    public function testRandomNumber()
    {
        $numbers = [];
        for ($i = 0; $i < 1000; $i++) {
            $number = get_secure_random_number();
            $this->assertTrue($number > 0);
            $this->assertTrue($number <= 2147483647);
            $numbers[] = $number;
        }
        $this->assertTrue(count(array_unique($numbers)) == count($numbers));
    }

    public function testRandomString()
    {
        $strings = [];
        for ($i = 0; $i < 100000; $i++) {
            $string = get_secure_random_string();
            $this->assertTrue(strlen($string) == 13);
            $strings[] = $string;
        }
        $this->assertTrue(count(array_unique($strings)) == count($strings));
    }

    public function testRatchet()
    {
        $password = get_secure_random_password();
        $salt = get_secure_random_string();
        $pass_hash_salted = ratchet_hash($password, $salt);
        $this->assertTrue(ratchet_hash_verify($password, $salt, $pass_hash_salted));
    }

    public function testObfuscate()
    {
        $email = 'foo@example.com';
        $this->assertTrue(strip_html(obfuscate_email_address($email)) == $email);
    }
}
