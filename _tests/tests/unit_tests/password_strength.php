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
class password_strength_test_set extends cms_test_case
{
    public function testPasswordStrength()
    {
        require_code('password_rules');

        $username = 'theusername';
        $email_address = 'bob@example.com';

        $expects = array(
            '' => 1,
            'theusername' => 1,
            'useruseruseruseruseruseruseruseruseruser' => 1,
            'usernamexyzabc' => 2,
            'abc123' => 3,
            'abc123d' => 4,
            'AbC123D' => 5,
            'AbC123De' => 7,
            'AbC123DeF' => 8,
            'AbC123De#' => 9,
            'Aa1#$acthfegrehde' => 10,
        );

        require_code('spelling');
        $spell_checker = _find_spell_checker();
        if ($spell_checker !== null) {
            $expects['hippopotamus'] = 1; // Dictionary word
        }

        foreach ($expects as $password => $score_expected) {
            $score_got = test_password($password, $username, $email_address);
            $this->assertTrue($score_got == $score_expected, 'For ' . $password . ', got ' . integer_format($score_got) . ' expected ' . integer_format($score_expected));
        }
    }
}
