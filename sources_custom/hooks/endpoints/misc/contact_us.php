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
class Hook_endpoint_misc_contact_us
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
            'authorization' => false,
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

        if (!addon_installed('tickets')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        // Gather input
        $id = uniqid('', true);
        $category = post_param_string('category', do_lang('GENERAL'));
        $post = post_param_string('post');
        $title = post_param_string('title', '');
        $email_from = post_param_string('email', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()), INPUT_FILTER_POST_IDENTIFIER);
        $from_name = substr(post_param_string('name', $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true), INPUT_FILTER_POST_IDENTIFIER), 0, 80);

        // Send notification
        require_code('notifications');
        require_lang('tickets');
        $notification_subject = do_lang('CONTACT_US_NOTIFICATION_SUBJECT', $title, null, null, get_site_default_lang());
        $notification_message = do_lang('CONTACT_US_NOTIFICATION_MESSAGE', comcode_escape(get_site_name()), comcode_escape($GLOBALS['FORUM_DRIVER']->get_username(get_member())), [$post, comcode_escape($category)], get_site_default_lang());
        dispatch_notification('ticket_reply', $type . '_' . $id, $notification_subject, $notification_message, null, null, ['create_ticket' => true]);

        // Send standard confirmation e-mail to current user
        if ($email_from != '' && get_option('message_received_emails') == '1') {
            require_code('mail');
            dispatch_mail(do_lang('YOUR_MESSAGE_WAS_SENT_SUBJECT', $title), do_lang('YOUR_MESSAGE_WAS_SENT_BODY', $post), '', [$email_from], ($from_name == '') ? null : $from_name, '', '', ['as' => get_member()]);
        }

        // Return
        return [
            'message' => do_lang('YOUR_MESSAGE_WAS_SENT_SUBJECT', $title),
        ];
    }
}
