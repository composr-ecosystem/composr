<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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

        require_code('notifications');
        require_code('hooks/systems/notifications/cns_birthday');
        require_code('hooks/systems/notifications/cns_friend_birthday');

        $GLOBALS['SITE_DB']->query_delete('notifications_enabled');
        $GLOBALS['SITE_DB']->query_delete('notification_lockdown');
        $GLOBALS['SITE_DB']->query_delete('member_zone_access');

        $all_members = $GLOBALS['FORUM_DB']->query_select('f_members', array('id'), null, 'WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND m_validated=1 AND ' . db_string_equal_to('m_validated_email_confirm_code', ''));

        foreach ($all_members as $member) {
            $GLOBALS['SITE_DB']->query_insert('member_zone_access', array(
                'zone_name' => 'site',
                'member_id' => $member['id'],
                'active_until' => null,
            ));
        }

        // Check default empty state...

        $ob = new Hook_notification_cns_birthday();
        $results = $ob->list_members_who_have_enabled('cns_birthday');
        $this->assertTrue(count($results[0]) == 0);
        $results = $ob->list_members_who_have_enabled('cns_birthday', null, array(get_member())); // Just make sure the member-ID filter doesn't crash

        $ob = new Hook_notification_cns_friend_birthday();
        $results = $ob->list_members_who_have_enabled('cns_friend_birthday');
        $this->assertTrue(count($results[0]) == count($all_members));

        // Check explicitly flipped state...

        foreach ($all_members as $member) {
            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', array(
                'l_member_id' => $member['id'],
                'l_notification_code' => 'cns_birthday',
                'l_code_category' => '',
                'l_setting' => A_INSTANT_EMAIL,
            ));

            $GLOBALS['SITE_DB']->query_insert('notifications_enabled', array(
                'l_member_id' => $member['id'],
                'l_notification_code' => 'cns_friend_birthday',
                'l_code_category' => '',
                'l_setting' => A_NA,
            ));
        }

        $ob = new Hook_notification_cns_birthday();
        $results = $ob->list_members_who_have_enabled('cns_birthday');
        $this->assertTrue(count($results[0]) == count($all_members));

        $ob = new Hook_notification_cns_friend_birthday();
        $results = $ob->list_members_who_have_enabled('cns_friend_birthday');
        $this->assertTrue(count($results[0]) == 0);

        // Check with locking...

        $GLOBALS['SITE_DB']->query_insert('notification_lockdown', array(
            'l_notification_code' => 'cns_birthday',
            'l_setting' => A_NA,
        ));
        $GLOBALS['SITE_DB']->query_insert('notification_lockdown', array(
            'l_notification_code' => 'cns_friend_birthday',
            'l_setting' => A_INSTANT_EMAIL,
        ));

        $ob = new Hook_notification_cns_birthday();
        $results = $ob->list_members_who_have_enabled('cns_birthday');
        $this->assertTrue(count($results[0]) == 0);

        $ob = new Hook_notification_cns_friend_birthday();
        $results = $ob->list_members_who_have_enabled('cns_friend_birthday');
        $this->assertTrue(count($results[0]) == count($all_members));
    }

    public function tearDown()
    {
        $GLOBALS['SITE_DB']->query_delete('notifications_enabled');
        $GLOBALS['SITE_DB']->query_delete('notification_lockdown');

        parent::tearDown();
    }
}
