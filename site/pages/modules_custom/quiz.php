<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    challengr
 */

function init__site__pages__modules_custom__quiz($in)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('quizzes')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('quizzes')));
    }

    if (!addon_installed('challengr')) {
        return $in;
    }

    $in = override_str_replace_exactly(
        "\$entry_id = \$GLOBALS['SITE_DB']->query_insert('quiz_entries', [",
        "
        if (addon_installed('points')) {
            require_code('points2');
            \$cost = \$quiz['q_points_for_passing'] / 2;
            charge_member(get_member(), \$cost, 'Entered a test');
        }
        <ditto>
        ",
        $in
    );

    return $in;
}
