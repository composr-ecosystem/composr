<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    challengr
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_challengr
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('challengr')) {
            return;
        }

        if (!addon_installed('quizzes')) {
            return;
        }

        if (!addon_installed('points')) {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'site/pages/modules/quiz.php':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                // Charge for entering the competition
                insert_code_after__by_command(
                    $code,
                    '_do_quiz',
                    "// We have not already got points",
                    "
                    \$cost = intval(ceil(floatval(\$quiz['q_points_for_passing']) / 2.0));
                    if (\$cost > 0) {
                        require_code('points2');
                        require_code('content');
                        list(\$title) = content_get_details('quiz', strval(\$quiz['id']));
                        points_debit_member(get_member(), \"Entered the test, '\" . \$title . \"'\", \$cost, 0, 0, false, 0, 'quiz', 'enter_test', strval(\$quiz['id']));
                        \$points_difference -= \$cost;
                    }
                    ");

                // Remove cost from awarded points
                insert_code_after__by_command(
                    $code,
                    '_do_quiz',
                    "// Give them their result if it is a test.",
                    "
                    if ((addon_installed('points')) && (\$quiz['q_points_for_passing'] != 0)) {
                        require_code('points2');
                        \$cost = intval(floor(\$quiz['q_points_for_passing'] / 2));
                        \$points_difference -= \$cost;
                    }
                    ");
                break;
        }
    }
}
