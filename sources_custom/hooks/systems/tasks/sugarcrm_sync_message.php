<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_task_sugarcrm_sync_message
{
    /**
     * Run the task hook.
     *
     * @param  string $subject The subject of the message
     * @param  string $body The body of the message
     * @param  EMAIL $to_email E-mail address to send to
     * @param  string $to_name The recipient name. Array or string
     * @param  EMAIL $from_email The from address
     * @param  string $from_name The from name
     * @param  array $attachments A list of attachments (each attachment being a map, absolute path=>filename)
     * @param  array $body_parts Body parts
     * @param  string $body_prefix The prefix text to the e-mail body (blank: none)
     * @param  string $body_suffix The suffix text to the e-mail body (blank: none)
     * @param  ?array $get Copy of GET parameters (null: don't set)
     * @param  ?array $post Copy of POST parameters (null: don't set)
     * @return mixed A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message) (false: re-try later, no specific error message)
     */
    public function run($subject, $body, $to_email, $to_name, $from_email, $from_name, $attachments, $body_parts, $body_prefix, $body_suffix, $get = null, $post = null)
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        if ($get !== null) {
            $_GET = $get;
        }
        if ($post !== null) {
            $_POST = $post;
        }

        require_code('sugarcrm');
        sugarcrm_initialise_connection();

        global $SUGARCRM;

        if ($SUGARCRM === null) {
            return false;
        }

        try {
            $success = save_message_into_sugarcrm_as_configured(($subject == get_site_name()) ? '' : $subject, $body, $from_email, $from_name, $attachments, $body_parts, $_POST + $_GET + $_COOKIE);
        } catch (Exception $e) {
            sugarcrm_failed($e->getMessage());
            return false;
        }

        if (!$success) {
            return false;
        }

        return null;
    }
}
