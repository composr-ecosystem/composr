<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Hook_task_sugarcrm_sync_member
{
    /**
     * Run the task hook.
     *
     * @param  MEMBER $member_id Member ID
     * @param  ?array $get Copy of GET parameters (null: don't set)
     * @param  ?array $post Copy of POST parameters (null: don't set)
     * @return mixed A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message) (false: re-try later, no specific error message)
     */
    public function run(int $member_id, ?array $get = null, ?array $post = null)
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

        $email_address = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id);
        if ($email_address == '') {
            return null; // No real details for user
        }

        require_code('sugarcrm');
        try {
            $success = sugarcrm_initialise_connection();
            if (!$success) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        try {
            $contact_id = save_composr_account_into_sugarcrm_as_configured($member_id);
        } catch (Exception $e) {
            require_code('failure');
            cms_error_log('SugarCRM: ' . $e->getMessage(), 'error_occurred_api');
            return false;
        }

        return null;
    }
}
