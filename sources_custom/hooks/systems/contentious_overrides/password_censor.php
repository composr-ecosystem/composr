<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_password_censor
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (($codename != 'notifications') || (strpos($path, 'sources_custom/') !== false)) {
            return;
        }

        if (!addon_installed('password_censor')) {
            return;
        }

        if ($code === null) {
            $code = clean_php_file_for_eval(file_get_contents($path));
        }

        require_code('override_api');

        insert_code_before__by_command(
            $code,
            'dispatch_notification',
            "\$dispatcher = new Notification_dispatcher",
            "
            if (
                // Existing private topic?
                (
                    (\$notification_code == 'cns_topic') &&
                    (is_numeric(\$code_category)) &&
                    (\$GLOBALS['FORUM_DB']->query_select_value_if_there('f_topics', 't_forum_id', ['id' => intval(\$code_category)]) === null)
                ) ||

                // New private topic?
                (\$notification_code == 'cns_new_pt') ||

                // Support ticket?
                (\$notification_code == 'ticket_new_staff') ||
                (\$notification_code == 'ticket_reply') ||
                (\$notification_code == 'ticket_reply_staff')
            ) {
                // These are all things we need to censor
                require_code('password_censor');
                \$message = _password_censor(\$message, PASSWORD_CENSOR__INTERACTIVE_SCAN);
            }
            ");
    }
}
