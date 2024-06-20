<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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

    require_code('override_api');

    // Make sure we reason our transactions for Idolisr properly
    insert_code_after__by_command(
        $in,
        'do_transact',
        "\$reason = post_param_string('reason');",
        "
        \$give_reason_pre = post_param_string('give_reason_pre', '');
        if (\$give_reason_pre != '') {
            \$reason = \$give_reason_pre . \": \" . \$reason;
        }
        ",
    );

    return $in;
}
