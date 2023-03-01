<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Composr API helper class.
 */
class CMSSubscriptionWrite
{
    /**
     * Set up notifications on a forum.
     *
     * @param  AUTO_LINK $forum_id Forum ID
     */
    public function subscribe_forum(int $forum_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';
        require_code('notifications');

        enable_notifications($notification_code, 'forum:' . strval($forum_id), $member_id);
    }

    /**
     * Remove notifications on a forum.
     *
     * @param  ?AUTO_LINK $forum_id Forum ID (null: all)
     */
    public function unsubscribe_forum(?int $forum_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';
        require_code('notifications');

        if ($forum_id === null) {
            $subscriptions = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', ['l_code_category'], ['l_notification_code' => $notification_code, 'l_member_id' => $member_id], ' AND l_code_category LIKE \'forum:%\'');
            foreach ($subscriptions as $subs) {
                disable_notifications($notification_code, $subs['l_code_category'], $member_id);
            }
        } else {
            disable_notifications($notification_code, 'forum:' . strval($forum_id), $member_id);
        }
    }

    /**
     * Set up notifications on a topic.
     *
     * @param  AUTO_LINK $topic_id Topic ID
     */
    public function subscribe_topic(int $topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';
        require_code('notifications');

        enable_notifications($notification_code, strval($topic_id), $member_id);
    }

    /**
     * Remove notifications on a topic.
     *
     * @param  ?AUTO_LINK $topic_id Topic ID (null: all)
     */
    public function unsubscribe_topic(?int $topic_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $member_id = get_member();

        $notification_code = 'cns_topic';
        require_code('notifications');

        if ($topic_id === null) {
            $subscriptions = $GLOBALS['FORUM_DB']->query_select('notifications_enabled', ['l_code_category'], ['l_notification_code' => $notification_code, 'l_member_id' => $member_id], ' AND l_code_category NOT LIKE \'forum:%\'');
            foreach ($subscriptions as $subs) {
                if (is_numeric($subs['l_code_category'])) {
                    disable_notifications($notification_code, $subs['l_code_category'], $member_id);
                }
            }
        } else {
            disable_notifications($notification_code, strval($topic_id), $member_id);
        }
    }
}
