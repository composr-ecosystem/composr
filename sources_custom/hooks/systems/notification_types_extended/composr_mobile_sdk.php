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
class Hook_notification_types_extended_composr_mobile_sdk
{
    /**
     * Standard initialisation function.
     */
    public function init()
    {
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
     * @return array Map of notification types (integer code to language string ID)
     * @ignore
     */
    public function _get_available_notification_types($member_id_of)
    {
        require_lang('composr_mobile_sdk');

        $__notification_types = array(
            A_INSTANT_IOS => 'NOTIFICATIONS_INSTANT_IOS',
            A_INSTANT_ANDROID => 'NOTIFICATIONS_INSTANT_ANDROID',
        );
        $_notification_types = array();
        foreach ($__notification_types as $possible => $ntype) {
            if (_notification_setting_available($possible, $member_id_of)) {
                $_notification_types[$possible] = $ntype;
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
     * @return integer Normal setting
     */
    public function _find_member_statistical_notification_type($to_member_id, $notification_code, $aggressive_pre_search)
    {
        $_notification_codes_for_mobile = get_option('notification_codes_for_mobile');
        $notification_codes_for_mobile = ($_notification_codes_for_mobile == '') ? array() : explode(',', $_notification_codes_for_mobile);
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
    public function _notification_setting_available($setting, $member_id, &$system_wide, &$for_member)
    {
        if (!$GLOBALS['SITE_DB']->table_exists('device_token_details')) {
            return;
        }

        switch ($setting) {
            case A_INSTANT_IOS:
                if (get_option('enable_notifications_instant_ios') === '1') {
                    $system_wide = true;
                    $token = $GLOBALS['SITE_DB']->query_select_value_if_there('device_token_details', 'device_token', array('member_id' => $member_id, 'token_type' => 'ios'));
                    if ($system_wide && !is_null($token)) {
                        $for_member = true;
                    }
                }
                break;

            case A_INSTANT_ANDROID:
                if (get_option('enable_notifications_instant_android') === '1') {
                    $system_wide = true;
                    $token = $GLOBALS['SITE_DB']->query_select_value_if_there('device_token_details', 'device_token', array('member_id' => $member_id, 'token_type' => 'android'));
                    if ($system_wide && !is_null($token)) {
                        $for_member = true;
                    }
                }
                break;
        }
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
     * @param  boolean $no_cc Whether to NOT CC to the CC address
     * @param  ?array $attachments A list of attachments (each attachment being a map, path=>filename) (null: none)
     * @param  boolean $use_real_from Whether we will make a "reply to" direct -- we only do this if we're allowed to disclose email addresses for this particular notification type (i.e. if it's a direct contact)
     * @return boolean New $no_cc setting
     */
    public function _dispatch_notification_to_member($to_member_id, $setting, $notification_code, $code_category, $subject, $message, $from_member_id, $priority, $no_cc, $attachments, $use_real_from)
    {
        if (get_option('enable_notifications_instant_ios') === '1') {
            if (_notification_setting_available(A_INSTANT_IOS, $to_member_id)) {
                $message = strip_comcode($message);
                $properties = $this->improve_message_for_mobile($message);

                require_code('tasks');
                $args = array($to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from);
                require_lang('composr_mobile_sdk');
                call_user_func_array__long_task(do_lang('NOTIFICATIONS_INSTANT_ANDROID'), get_screen_title('NOTIFICATIONS_INSTANT_ANDROID', true, null, null, null, false), 'ios_notification', $args, false, false, false);
            }
        }

        if (get_option('enable_notifications_instant_android') !== '') {
            if (_notification_setting_available(A_INSTANT_ANDROID, $to_member_id)) {
                $message = strip_comcode($message);
                $properties = $this->improve_message_for_mobile($message);

                require_code('tasks');
                $args = array($to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from);
                require_lang('composr_mobile_sdk');
                call_user_func_array__long_task(do_lang('NOTIFICATIONS_INSTANT_IOS'), get_screen_title('NOTIFICATIONS_INSTANT_IOS', true, null, null, null, false), 'android_notification', $args, false, false, false);
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
    private function improve_message_for_mobile(&$message)
    {
        $properties = array();

        // Re-processing of language string for iOS specifically (we can customise it so long as do_notification_lang was used).
        global $LAST_NOTIFICATION_LANG_CALL;
        if (!is_null($LAST_NOTIFICATION_LANG_CALL)) {
            require_lang('composr_mobile_sdk');

            list($codename, $parameter1, $parameter2, $parameter3, $lang, ) = $LAST_NOTIFICATION_LANG_CALL;
            if (!is_null($parameter1)) {
                $properties['do_lang_1'] = is_object($parameter1) ? $parameter1->evaluate() : $parameter1;
            }
            if (!is_null($parameter2)) {
                $properties['do_lang_2'] = is_object($parameter2) ? $parameter2->evaluate() : $parameter2;
            }
            if (!is_null($parameter3)) {
                if (is_array($parameter3)) {
                    foreach ($parameter3 as $i => $param) {
                        $properties['do_lang_' . strval(3 + $i)] = is_object($param) ? $param->evaluate() : $param;
                    }
                } else {
                    $properties['do_lang_3'] = is_object($parameter3) ? $parameter3->evaluate() : $parameter3;
                }
            }
            $_message = do_lang('MOBILE__' . $codename, $parameter1, $parameter2, $parameter3, $lang, false);
            if (!is_null($_message)) {
                $message = $_message;
            }
        }

        // Re-processing of template for iOS specifically (we can customise it so long as do_notification_template was used).
        global $LAST_NOTIFICATION_TEMPLATE_CALL;
        if (!is_null($LAST_NOTIFICATION_TEMPLATE_CALL)) {
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
