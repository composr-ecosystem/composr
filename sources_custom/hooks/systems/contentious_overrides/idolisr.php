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

/**
 * Hook class.
 */
class Hook_contentious_overrides_idolisr
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('idolisr')) {
            return;
        }

        if (!addon_installed('points')) {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'points2':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

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
                break;
            case 'site/pages/modules/points.php':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                // Make sure we reason our transactions for Idolisr properly
                insert_code_after__by_command(
                    $code,
                    'do_transact',
                    "\$reason = post_param_string('reason');",
                    "
                    \$give_reason_pre = post_param_string('give_reason_pre', '');
                    if (\$give_reason_pre != '') {
                        \$reason = \$give_reason_pre . \": \" . \$reason;
                    }
                    ");
                break;
        }
    }
}
