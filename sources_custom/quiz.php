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

function init__quiz($in = null)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('challengr')) {
        return $in;
    }

    $in = override_str_replace_exactly(
        "if (\$minimum_percentage >= \$quiz['q_percentage'])",
        "
        if ((addon_installed('points')) && (\$quiz['q_points_for_passing'] != 0)) {
            require_code('points2');
            \$cost = \$quiz['q_points_for_passing'] / 2;
            \$points_difference -= \$cost;
        }
        <ditto>
        ",
        $in
    );

    return $in;
}
