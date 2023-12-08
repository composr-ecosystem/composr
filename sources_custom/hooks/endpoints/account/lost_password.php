<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set).
     * @param  ?string $id Standard ID parameter (null: not-set).
     * @return array Data structure that will be converted to correct response type.
     */
    public function run($type, $id)
    {
        $username = trim(either_param_string('username', ''));
        $email_address = trim(either_param_string('email_address', ''));

        require_code('cns_lost_password');
        require_lang('cns');
        lost_password_emailer_step($username, $email_address);

        return array(
            'message' => do_lang('RESET_CODE_MAILED'),
        );
    }
}
