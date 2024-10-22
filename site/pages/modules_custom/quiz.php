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

function init__site__pages__modules_custom__quiz($in)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('quizzes')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('quizzes')));
    }

    if ((!addon_installed('challengr')) || (!addon_installed('points'))) {
        return $in;
    }

    require_code('override_api');

    // Charge for entering the competition
    insert_code_after__by_command(
        $in,
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
        $in,
        '_do_quiz',
        "// Give them their result if it is a test.",
        "
        if ((addon_installed('points')) && (\$quiz['q_points_for_passing'] != 0)) {
            require_code('points2');
            \$cost = intval(floor(\$quiz['q_points_for_passing'] / 2));
            \$points_difference -= \$cost;
        }
        ");

    return $in;
}
