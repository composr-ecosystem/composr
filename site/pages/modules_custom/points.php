<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    idolisr
 */

function init__site__pages__modules_custom__points($in)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }

    if (!addon_installed('idolisr')) {
        return $in;
    }

    $in = override_str_replace_exactly(
        "\$reason = post_param_string('reason');",
        "
        <ditto>
        \$give_reason_pre = post_param_string('give_reason_pre', '');
        if (\$give_reason_pre != '') {
            \$reason = \$give_reason_pre . \": \" . \$reason;
        }
        ",
        $in
    );

    return $in;
}
