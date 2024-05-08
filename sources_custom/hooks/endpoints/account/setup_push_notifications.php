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
class Hook_endpoint_account_setup_push_notifications
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => ['member'],
        ];
    }

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

        if (is_guest(get_member())) {
            access_denied('NOT_AS_GUEST');
        }

        // Store a device notification token (i.e. identification of a device, so we can send notifications to it).

        $token_type = either_param_string('device'); // iOS|android
        $token = either_param_string('token');

        $member_details = $GLOBALS['FORUM_DB']->query_select('f_members', ['id'], ['id' => get_member()], '', 1);
        if (!isset($member_details[0])) {
            warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'), false, false, 404);
        }

        $GLOBALS['SITE_DB']->query_delete('device_token_details', ['member_id' => get_member(), 'token_type' => $token_type]);
        $GLOBALS['SITE_DB']->query_insert('device_token_details', [
            'token_type' => $token_type,
            'member_id' => get_member(),
            'device_token' => $token,
        ]);
        return ['message' => do_lang('SUCCESS')];
    }
}
