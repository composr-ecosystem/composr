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
class password_strength_test_set extends cms_test_case
{
    public function testPasswordStrength()
    {
        require_code('password_rules');

        $username = 'theU$ERname';
        $email_address = 'bob@example.com';

        $expects = [
            // Score 1 tests
            '' => 1,
            'aaa' => 1,
            'XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx' => 1,
            'useruseruseruseruseruseruseruseruseruser' => 1,
            '1234567890' => 1,

            // Score 1 tainted tests
            'theU$ERname' => 1,
            'bob@example.com' => 1,

            // Sequential strengths with intentional passwords test
            'abc123' => 1,
            'abcd123' => 2,
            'abcD123' => 3,
            '@bcD123' => 4,
            '@bcD1234' => 5,
            '@bcDe1234' => 6,
            '@bcDef1234$' => 7,
            '@bcDefG1234$' => 8,
            '@bcDefGH1234$6' => 9,
            '@bcDefGH1234$67' => 10,

            // Sequential strengths with random passwords test
            'p' => 1,
            'Ca0L' => 2,
            'm.X7%' => 3,
            'BCI-u=q' => 4,
            '&dkXouA(' => 5,
            'q11&SPNgyJ' => 6,
            'dx+F,Y1tG0b' => 7,
            'OXE9iNmjM1!K' => 8,
            'dfyALhO)Eh3b78V' => 9,
            '64_WpFYiBQd4Ka\'M0' => 10,
        ];

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
