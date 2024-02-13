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
 * iOS notifications sender class.
 */
class IOSPushNotifications
{
    private $has_registered_shutdown = false;

    /**
     * Called to process the notification for iOS.
     *
     * @param  MEMBER $to_member_id Member to send to
     * @param  ID_TEXT $notification_code The notification code to use
     * @param  ?SHORT_TEXT $code_category The category within the notification code (null: none)
     * @param  SHORT_TEXT $subject Message subject (in Comcode)
     * @param  LONG_TEXT $message Message body
     * @param  array $properties Custom properties to add to outbound message
     * @param  integer $from_member_id The member ID doing the sending. Either a MEMBER or a negative number (e.g. A_FROM_SYSTEM_UNPRIVILEGED)
     * @param  integer $priority The message priority (1=urgent, 3=normal, 5=low)
     * @range  1 5
     * @param  boolean $no_cc Whether to NOT CC to the CC address
     * @param  array $attachments A list of attachments (each attachment being a map, path=>filename)
     * @param  boolean $use_real_from Whether we will make a "reply to" direct -- we only do this if we're allowed to disclose email addresses for this particular notification type (i.e. if it's a direct contact)
     */
    private function apns_dispatch($to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from)
    {
        $push = $this->initialise_apns();

        $token = $GLOBALS['SITE_DB']->query_select_value('device_token_details', 'device_token', ['member_id' => $to_member_id, 'token_type' => 'ios']);
        $message_ob = new ApnsPHP_Message_Custom($token);

        $message_ob->setTitle($subject); // For Apple Watch, or possibly newer versions of iOS
        $message_ob->setText($message);

        // NOTE: Implement 'priority' if apnsPHP implements the new binary protocol

        // Some extra metadata that may be useful...

        $message_ob->setCustomIdentifier($notification_code . '_' . (($code_category === null) ? '' : $code_category) . '_' . strval(get_member()) . '_' . strval(time()));

        $data = [];
        $data['notification_code'] = $notification_code;
        $data['code_category'] = ($code_category === null) ? '' : $code_category;
        $data['from_id'] = strval($from_member_id);
        $data['from_username'] = ($from_member_id >= 0) ? $GLOBALS['FORUM_DRIVER']->get_username($from_member_id, false, USERNAME_DEFAULT_NULL) : '';
        $data['from_displayname'] = ($from_member_id >= 0) ? $GLOBALS['FORUM_DRIVER']->get_username($from_member_id, true, USERNAME_DEFAULT_NULL) : '';
        $data['from_avatar_url'] = ($from_member_id >= 0) ? $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($from_member_id) : '';
        $data['from_photo_url'] = ($from_member_id >= 0) ? $GLOBALS['FORUM_DRIVER']->get_member_photo_url($from_member_id) : '';
        $data['to_id'] = strval($to_member_id);
        $data['to_username'] = $GLOBALS['FORUM_DRIVER']->get_username($to_member_id, false, USERNAME_DEFAULT_NULL);
        $data['to_displayname'] = $GLOBALS['FORUM_DRIVER']->get_username($to_member_id, true, USERNAME_DEFAULT_NULL);

        uasort($properties, 'strlen');
        $properties = array_reverse($properties, true);
        foreach ($properties as $key => $val) {
            if (strlen(serialize($data)) > 800) {
                break;
            }

            $data[$key] = $val;
        }

        foreach ($data as $key => $val) {
            $message_ob->setCustomProperty($key, $val);
        }

        // ---

        // Add the message to the message queue
        $push->add($message_ob);

        // Empty queue
        if (!$this->has_registered_shutdown) {
            register_shutdown_function([$this, 'flush_apns']);
            $this->has_registered_shutdown = true;
        }
    }

    /**
     * Called to initialise the push configuration.
     *
     * @return object Instantiation of a new ApnsPHP_Push object
     */
    private function initialise_apns()
    {
        static $push = null;
        if ($push !== null) {
            return $push;
        }

        require_code('composr_mobile_sdk/ios/ApnsPHP/Autoload');

        date_default_timezone_set(get_site_timezone());

        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
            get_custom_file_base() . '/data_custom/modules/ios/server_certificates.pem'
        );

        $passphrase = get_option('ios_cert_passphrase');
        if ($passphrase != '') {
            $push->setProviderCertificatePassphrase($passphrase);
        }

        // Set the Root Certificate Autority to verify the Apple remote peer
        //$push->setRootCertificationAuthority($FILE_BASE . '/data_custom/modules/ios/entrust_root_certification_authority.pem');

        return $push;
    }

    /**
     * Send all APNS messages.
     */
    private function flush_apns()
    {
        $push = $this->initialise_apns();

        // Send all messages in the message queue
        $push->connect();
        $push->send();
        $push->disconnect();

        require_code('failure');

        // Examine the error message container
        $error_queue = $push->getErrors();
        if (!empty($error_queue)) {
            $all_errors = [];
            foreach ($error_queue as $message_arr) {
                foreach ($message_arr['ERRORS'] as $error) {
                    $all_errors[$error['statusMessage']] = true; // Put in as array keys, to de-duplicate
                }
            }
            foreach (array_keys($all_errors) as $error) {
                cms_error_log($error);
            }
        }
    }
}
