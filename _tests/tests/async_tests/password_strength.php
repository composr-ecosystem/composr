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
class password_strength_test_set extends cms_test_case
{
    public function testPasswordStrength()
    {
        require_code('password_rules');

        $username = 'theU$ERname';
        $email_address = 'bob@example.com';

        $expects = [
            // Test tainted passwords
            'theU$ERname' => 1,
            'bob@example.com' => 1,

            // Special tests
            '' => 1, // Empty
            'abCIYZ' => 2, // Test that abc subtracts 1
            'sjh876BR' => 2, // Test that 876 subtracts 2

            // Special tests: repeat character deductions
            'CCCCC^jk9I6c&h8cE' => 7,
            'CCCCCcCc9I6c&h8cE' => 4,
            'CCCCCCCCCCCcCcC1#' => 1,

            // Random passwords test
            'T' => 1,
            'oWbE' => 2,
            '0LR1wp' => 3,
            'R9=.K~C' => 4,
            '3n21Q;Xi' => 5,
            'Aw7HUQ%;6~' => 6,
            '_GWfe^t2E;-' => 7,
            'qDLp02xKy8@a7' => 8,
            'ypEWDE8s0G8082e5' => 9,
            'fxg%xo~?9J`8Y3|E' => 10,
        ];

        require_code('spelling');
        $spell_checker = _find_spell_checker();
        if ($spell_checker !== null) {
            $expects['hippopotamus'] = 1; // Dictionary word
        }

        foreach ($expects as $password => $score_expected) {
            $score_got = test_password(strval($password), $username, $email_address);
            $this->assertTrue($score_got == $score_expected, 'For ' . $password . ', got ' . integer_format($score_got) . ' expected ' . integer_format($score_expected));
        }
    }
}
