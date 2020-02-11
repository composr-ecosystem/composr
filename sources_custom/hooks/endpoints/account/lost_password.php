<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function run($type, $id)
    {
        if (!addon_installed('composr_mobile_sdk')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $username = trim(either_param_string('username', ''));
        $email_address = trim(either_param_string('email', ''));

        require_code('cns_lost_password');
        require_lang('cns');
        lost_password_emailer_step($username, $email_address);

        return [
            'message' => do_lang('RESET_CODE_MAILED'),
        ];
    }
}
