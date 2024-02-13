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
            $this->assertTrue(is_valid_email_address($string) == $status, 'Incorrect e-mail address status for ' . $string);
        }
    }
}
