<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_endpoint_account_lost_password
{
    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('composr_mobile_sdk')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $_username = post_param_string('username', '', INPUT_FILTER_POST_IDENTIFIER);
        $_email = post_param_string('email', '', INPUT_FILTER_POST_IDENTIFIER);

        require_code('cns_lost_password');
        require_lang('cns');
        list($email, $member_id) = lost_password_emailer_step($_username, $_email);

        $password_reset_process = get_password_reset_process();

        $mailed_message = lost_password_mailed_message($password_reset_process, $email);

        return [
            'message' => $mailed_message->evaluate(),
        ];
    }
}
