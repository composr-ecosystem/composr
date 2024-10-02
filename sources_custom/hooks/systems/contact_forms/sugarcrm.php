<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_contact_forms_sugarcrm
{
    public function dispatch($subject, $body, $body_web, $to_email, $to_name, $from_email, $from_name, $attachments, $body_parts, $body_prefix, $body_suffix)
    {
        if (!addon_installed('sugarcrm')) {
            return false;
        }

        if (!function_exists('curl_init')) {
            return false;
        }

        require_code('sugarcrm');

        if (!sugarcrm_configured()) {
            return false;
        }

        $sugarcrm_skip_string = get_option('sugarcrm_skip_string');
        if (($sugarcrm_skip_string != '') && (strpos($body, $sugarcrm_skip_string) !== false)) {
            return true;
        }

        $_attachments = [];
        foreach ($attachments as $path => $filename) {
            if ((strpos($path, '://') === false) && (substr($path, 0, 5) != 'gs://')) {
                $path_new = get_custom_file_base() . '/temp/mail_' . uniqid('', true) . '.txt';
                copy($path, $path_new);
                fix_permissions($path_new);
                sync_file($path_new);

                $_attachments[$path_new] = $filename;
            } else {
                $_attachments[$path] = $filename;
            }
        }

        require_code('tasks');
        $_title = do_lang('SUGARCRM_MESSAGING_SYNC');
        call_user_func_array__long_task($_title, null, 'sugarcrm_sync_message', [$subject, $body, $to_email, $to_name, $from_email, $from_name, $_attachments, $body_parts, $body_prefix, $body_suffix, $_GET, $_POST], false, false, false);

        return (get_option('sugarcrm_exclusive_messaging') == '1') || (post_param_integer('_sugarcrm_exclusive_messaging', 0) == 1);
    }
}
