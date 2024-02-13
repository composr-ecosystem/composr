<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class notifications_test_set extends cms_test_case
{
    public function testNotificationsQuerying()
    {
        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        if ($GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)') > 300) {
            $this->assertTrue(false, 'Test will not work on databases with a lot of users');
            return;
        }

        require_code('notifications');
        require_code('hooks/systems/notifications/comment_posted');
        require_code('hooks/systems/notifications/points');

        $GLOBALS['SITE_DB']->query_delete('notifications_enabled');
        $GLOBALS['SITE_DB']->query_delete('notification_lockdown');
        $GLOBALS['SITE_DB']->query_delete('member_zone_access');

        $all_members = $GLOBALS['FORUM_DB']->query_select('f_members', ['id'], [], 'WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND m_validated=1 AND ' . db_string_equal_to('m_validated_email_confirm_code', ''));
        $GLOBALS['FORUM_DB']->query_update('f_members', ['m_allow_emails' => 1, 'm_allow_emails_from_staff' => 1]);

        foreach ($all_members as $member) {
            $GLOBALS['SITE_DB']->query_insert('member_zone_access', [
                'zone_name' => 'site',
                'member_id' => $member['id'],
                'active_until' => null,
            ]);
        }

        // Check default empty state...

        $ob = new Hook_notification_comment_posted();
        $results = $ob->list_members_who_have_enabled('comment_posted');
        $this->assertTrue(empty($results[0]));
        $results = $ob->list_members_who_have_enabled('comment_posted', null, [get_member()]); // Just make sure the member-ID filter doesn't crash

        $ob = new Hook_notification_points();
        $results = $ob->list_members_who_have_enabled('point_escrows');
        $this->assertTrue(count($results[0]) == count($all_members));

        // Check explicitly flipped state...

        foreach ($all_members as $member) {
            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', [
                'l_member_id' => $member['id'],
                'l_notification_code' => 'comment_posted',
                'l_code_category' => '',
                'l_setting' => A_INSTANT_EMAIL,
            ]);

            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', [
                'l_member_id' => $member['id'],
                'l_notification_code' => 'point_escrows',
                'l_code_category' => '',
                'l_setting' => A_NA,
            ]);
        }

        $ob = new Hook_notification_comment_posted();
        $results = $ob->list_members_who_have_enabled('comment_posted');
        $this->assertTrue(count($results[0]) == count($all_members));

        $ob = new Hook_notification_points();
        $results = $ob->list_members_who_have_enabled('point_escrows');
        $this->assertTrue(empty($results[0]));

        // Check with locking...

        $GLOBALS['SITE_DB']->query_insert('notification_lockdown', [
            'l_notification_code' => 'comment_posted',
            'l_setting' => A_NA,
        ]);
        $GLOBALS['SITE_DB']->query_insert('notification_lockdown', [
            'l_notification_code' => 'point_escrows',
            'l_setting' => A_INSTANT_EMAIL,
        ]);

        global $NOTIFICATION_LOCKDOWN_CACHE;
        $NOTIFICATION_LOCKDOWN_CACHE = [];

        $ob = new Hook_notification_comment_posted();
        $results = $ob->list_members_who_have_enabled('comment_posted');
        $this->assertTrue(empty($results[0]));

        $ob = new Hook_notification_points();
        $results = $ob->list_members_who_have_enabled('point_escrows');
        $this->assertTrue(count($results[0]) == count($all_members));
    }

    public function tearDown()
    {
        $GLOBALS['SITE_DB']->query_delete('notifications_enabled');
        $GLOBALS['SITE_DB']->query_delete('notification_lockdown');

        parent::tearDown();
    }
}
