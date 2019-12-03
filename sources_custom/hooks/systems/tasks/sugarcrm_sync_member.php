<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
    public function run($member_id, $get = null, $post = null)
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
        sugarcrm_initialise_connection();

        global $SUGARCRM;

        if ($SUGARCRM === null) {
            return false;
        }

        try {
            $contact_id = save_composr_account_into_sugarcrm_as_configured($member_id);
        } catch (Exception $e) {
            sugarcrm_failed($e->getMessage());
            return false;
        }

        return null;
    }
}
