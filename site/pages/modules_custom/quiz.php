<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    challengr
 */

function init__site__pages__modules_custom__quiz($in = null)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    $in = override_str_replace_exactly(
        "\$type = 'Test';",
        "
        <ditto>
        if (addon_installed('points')) {
            require_code('points2');
            \$cost = intval(floor(\$quiz['q_points_for_passing'] / 2));
            charge_member(get_member(), \$cost, 'Entered a test');
        }
        ",
        $in
    );

    $in = override_str_replace_exactly(
        "// Give them their result if it is a test.",
        "
        <ditto>
        if ((addon_installed('points')) && (\$quiz['q_points_for_passing'] != 0)) {
            require_code('points2');
            \$cost = intval(floor(\$quiz['q_points_for_passing'] / 2));
            \$points_difference -= \$cost;
        }
        ",
        $in
    );

    return $in;
}
