<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_endpoint_account_setup_push_notifications
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

        // Store a device notification token (i.e. identification of a device, so we can send notifications to it).

        $token_type = either_param_string('device'); // iOS|android
        $member_id = either_param_integer('member', get_member());
        $token = either_param_string('token');

        $member_details = $GLOBALS['FORUM_DB']->query_select('f_members', ['id'], ['id' => $member_id], '', 1);
        if (!isset($member_details[0])) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'), false, false, 404);
        }

        $GLOBALS['SITE_DB']->query_delete('device_token_details', ['member_id' => $member_id, 'token_type' => $token_type]);
        $GLOBALS['SITE_DB']->query_insert('device_token_details', [
            'token_type' => $token_type,
            'member_id' => $member_id,
            'device_token' => $token,
        ]);
        return ['message' => do_lang('SUCCESS')];
    }
}
