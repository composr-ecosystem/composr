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
class Hook_notification_types_extended_composr_mobile_sdk
{
    /**
     * Standard initialisation function.
     */
    public function init()
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return;
        }

        define('A_INSTANT_IOS', 0x100);         // (256 in decimal) iOS (not supplied by default in Composr, may be served via addon)
        define('A_INSTANT_ANDROID', 0x200);     // (512 in decimal) Android (not supplied by default in Composr, may be served via addon)

        define('A__MOBILE', A_INSTANT_IOS | A_INSTANT_ANDROID);

        global $ALL_NOTIFICATION_TYPES;
        $ALL_NOTIFICATION_TYPES[] = A_INSTANT_IOS;
        $ALL_NOTIFICATION_TYPES[] = A_INSTANT_ANDROID;
    }

    /**
     * Get a map of notification types available to our member.
     *
     * @param  ?MEMBER $member_id_of Member this is for (null: just check globally)
     * @return array Map of notification types (integer code to language string codename)
     * @ignore
     */
    public function _get_available_notification_types(?int $member_id_of) : array
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return [];
        }

        require_lang('composr_mobile_sdk');

        $__notification_types = [
            A_INSTANT_IOS => 'NOTIFICATIONS_INSTANT_IOS',
            A_INSTANT_ANDROID => 'NOTIFICATIONS_INSTANT_ANDROID',
        ];
        $_notification_types = [];
        foreach ($__notification_types as $notification_type_constant => $notification_type_codename) {
            if (_notification_setting_available($notification_type_constant, $member_id_of)) {
                $_notification_types[$notification_type_constant] = $notification_type_codename;
            }
        }
        return $_notification_types;
    }

    /**
     * Find what a member usually receives notifications on.
     *
     * @param  MEMBER $to_member_id Member to send to
     * @param  ID_TEXT $notification_code The notification code to use
     * @param  boolean $aggressive_pre_search Doing a pre-search without actually having statistical search first (usually we return on this, but we may choose to force a setting here)
     * @return ?integer Normal setting (null: unknown/unavailable)
     */
    public function _find_member_statistical_notification_type(int $to_member_id, string $notification_code, bool $aggressive_pre_search) : ?int
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return null;
        }

        $_notification_codes_for_mobile = get_option('notification_codes_for_mobile');
        $notification_codes_for_mobile = ($_notification_codes_for_mobile == '') ? [] : explode(',', $_notification_codes_for_mobile);
        if ($aggressive_pre_search && !in_array($notification_code, $notification_codes_for_mobile)) {
            return null;
        }

        if (_notification_setting_available(A_INSTANT_IOS, $to_member_id)) {
            return A_INSTANT_IOS;
        }

        if (_notification_setting_available(A_INSTANT_ANDROID, $to_member_id)) {
            return A_INSTANT_ANDROID;
        }

        return null;
    }

    /**
     * Find whether a particular kind of notification is available.
     *
     * @param  integer $setting The notification setting
     * @param  ?MEMBER $member_id Member to check for (null: just check globally)
     * @param  boolean $system_wide Is set system wide (return by reference)
     * @param  boolean $for_member Is set for member (return by reference)
     * @return boolean Whether it is available
     */
    public function _notification_setting_available(int $setting, ?int $member_id, bool &$system_wide, bool &$for_member) : bool
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return false;
        }

        switch ($setting) {
            case A_INSTANT_IOS:
                if (get_option('enable_notifications_instant_ios') === '1') {
                    $system_wide = true;
                    $token = $GLOBALS['SITE_DB']->query_select_value_if_there('device_token_details', 'device_token', ['member_id' => $member_id, 'token_type' => 'ios']);
                    if ($system_wide && $token !== null) {
                        $for_member = true;
                    }
                }
                break;

            case A_INSTANT_ANDROID:
                if (get_option('enable_notifications_instant_android') != '') {
                    $system_wide = true;
                    $token = $GLOBALS['SITE_DB']->query_select_value_if_there('device_token_details', 'device_token', ['member_id' => $member_id, 'token_type' => 'android']);
                    if ($system_wide && $token !== null) {
                        $for_member = true;
                    }
                }
                break;
        }

        return false;
    }

    /**
     * Send out a notification to a member.
     *
     * @param  MEMBER $to_member_id Member to send to
     * @param  integer $setting Listening setting
     * @param  ID_TEXT $notification_code The notification code to use
     * @param  ?SHORT_TEXT $code_category The category within the notification code (null: none)
     * @param  SHORT_TEXT $subject Message subject (in Comcode)
     * @param  LONG_TEXT $message Message body (in Comcode)
     * @param  integer $from_member_id The member ID doing the sending. Either a MEMBER or a negative number (e.g. A_FROM_SYSTEM_UNPRIVILEGED)
     * @param  integer $priority The message priority (1=urgent, 3=normal, 5=low)
     * @range  1 5
     * @param  boolean $no_cc Whether to NOT CC to the CC address (may be set to true and returned by reference once a single e-mail has been sent)
     * @param  array $attachments A list of attachments (each attachment being a map, path=>filename)
     * @param  boolean $use_real_from Whether we will make a "reply to" direct -- we only do this if we're allowed to disclose e-mail addresses for this particular notification type (i.e. if it's a direct contact)
     * @return boolean Whether a notification was sent
     */
    public function dispatch_notification_to_member(int $to_member_id, int $setting, string $notification_code, ?string $code_category, string $subject, string $message, int $from_member_id, int $priority, bool &$no_cc, array $attachments, bool $use_real_from) : bool
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return false;
        }

        if (get_option('enable_notifications_instant_ios') === '1') {
            if (_notification_setting_available(A_INSTANT_IOS, $to_member_id)) {
                $message = strip_comcode($message);
                $properties = $this->improve_message_for_mobile($message);

                require_code('tasks');
                $args = [$to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from];
                require_lang('composr_mobile_sdk');
                call_user_func_array__long_task(do_lang('NOTIFICATIONS_INSTANT_ANDROID'), null, 'ios_notification', $args, false, false, false);
            }
        }

        if (get_option('enable_notifications_instant_android') !== '') {
            if (_notification_setting_available(A_INSTANT_ANDROID, $to_member_id)) {
                $message = strip_comcode($message);
                $properties = $this->improve_message_for_mobile($message);

                require_code('tasks');
                $args = [$to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from];
                require_lang('composr_mobile_sdk');
                call_user_func_array__long_task(do_lang('NOTIFICATIONS_INSTANT_IOS'), null, 'android_notification', $args, false, false, false);
            }
        }

        return $no_cc;
    }

    /**
     * Adjust the message body to be more appropriate for mobile.
     *
     * @param  LONG_TEXT $message Message body (in Comcode) (returned by reference)
     * @return array Custom properties to add to outbound message
     */
    protected function improve_message_for_mobile(string &$message) : array
    {
        $properties = [];

        // Re-processing of language string for iOS specifically (we can customise it so long as do_notification_lang was used).
        global $LAST_NOTIFICATION_LANG_CALL;
        if ($LAST_NOTIFICATION_LANG_CALL !== null) {
            require_lang('composr_mobile_sdk');

            list($codename, $parameter1, $parameter2, $parameter3, $lang, ) = $LAST_NOTIFICATION_LANG_CALL;
            if ($parameter1 !== null) {
                $properties['do_lang_1'] = is_object($parameter1) ? $parameter1->evaluate() : $parameter1;
            }
            if ($parameter2 !== null) {
                $properties['do_lang_2'] = is_object($parameter2) ? $parameter2->evaluate() : $parameter2;
            }
            if ($parameter3 !== null) {
                if (is_array($parameter3)) {
                    foreach ($parameter3 as $i => $param) {
                        $properties['do_lang_' . strval(3 + $i)] = is_object($param) ? $param->evaluate() : $param;
                    }
                } else {
                    $properties['do_lang_3'] = is_object($parameter3) ? $parameter3->evaluate() : $parameter3;
                }
            }
            $_message = do_lang('MOBILE__' . $codename, $parameter1, $parameter2, $parameter3, $lang, false);
            if ($_message !== null) {
                $message = $_message;
            }
        }

        // Re-processing of template for iOS specifically (we can customise it so long as do_notification_template was used).
        global $LAST_NOTIFICATION_TEMPLATE_CALL;
        if ($LAST_NOTIFICATION_TEMPLATE_CALL !== null) {
            list($codename, $parameters, $lang, $light_error, , $suffix, $directory, $theme) = $LAST_NOTIFICATION_TEMPLATE_CALL;
            $_message_tempcode = do_template('MOBILE__' . $codename, $parameters, $lang, false, $codename, $suffix, $directory, $theme);
            $message = $_message_tempcode->evaluate();

            foreach ($parameters as $key => $val) {
                if (is_string($val) || is_object($val)) {
                    $properties[$key] = is_object($val) ? $val->evaluate() : $val;
                }
            }
        }

        return $properties;
    }
}
