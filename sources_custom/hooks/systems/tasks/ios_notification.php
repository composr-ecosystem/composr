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
class Hook_task_ios_notification
{
    /**
     * Run the task hook.
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
     * @param  ?array $attachments A list of attachments (each attachment being a map, path=>filename) (null: none)
     * @param  boolean $use_real_from Whether we will make a "reply to" direct -- we only do this if we're allowed to disclose email addresses for this particular notification type (i.e. if it's a direct contact)
     *
     * @return ?array A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message)
     */
    public function run($to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from)
    {
        require_code('composr_mobile_sdk/ios/notifications');
        $ob = new IOSPushNotifications();
        $this->ios_dispatch($to_member_id, $notification_code, $code_category, $subject, $message, $properties, $from_member_id, $priority, $no_cc, $attachments, $use_real_from);

        return null;
    }
}
