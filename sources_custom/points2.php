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

 function init__points2($code)
 {
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    require_code('override_api');

    insert_code_after__by_command(
        $code,
        'points_dispatch_notification',
        "// Leave this comment: Any code overrides attaching additional information to the sender notification should go here.",
        "
        \$roles = array_map('trim', explode(',', get_option('idolisr_roles')));
        if (preg_match('#^(' . implode('|', array_map('preg_quote', \$roles)) . '):#', \$reason) != 0) {
            require_lang('idolisr');
            \$message_raw->attach(do_notification_lang('IDOLISR_POINTS_SENT_L', \$their_displayname));
        }
        ",
        1,
        true
    );

    return $code;
 }
