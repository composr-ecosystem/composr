<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    password_censor
 */

if (!function_exists('init__notifications')) {
    function init__notifications($in = null)
    {
        $in = override_str_replace_exactly(
            "\$dispatcher = ",
            "
            if (
                // Existing private topic?
                (
                    (\$notification_code == 'cns_topic') &&
                    (is_numeric(\$code_category)) &&
                    (\$GLOBALS['FORUM_DB']->query_select_value_if_there('f_topics', 't_forum_id', array('id' => intval(\$code_category))) === null)
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
            <ditto>
            ",
            $in
        );

        return $in;
    }
}
