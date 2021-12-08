<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class bump_member_group_timeout_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $GLOBALS['NO_QUERY_LIMIT'] = true;

        $GLOBALS['FORUM_DB']->query_delete('f_group_member_timeouts');
        $GLOBALS['FORUM_DB']->query_delete('f_group_members');

        require_code('group_member_timeouts');
    }

    public function testMemberGroupTimeoutSecondary()
    {
        $member_id = 3;
        $group_id = 4;

        bump_member_group_timeout($member_id, $group_id, -10, false);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutPrimary()
    {
        $member_id = 3;
        $group_id = 4;

        bump_member_group_timeout($member_id, $group_id, -10, true);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutKickout()
    {
        $member_id = 3;
        $group_id = 4;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        cleanup_member_timeouts();
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutTimeAddition()
    {
        $member_id = 3;
        $group_id = 4;

        bump_member_group_timeout($member_id, $group_id, -10, false);
        bump_member_group_timeout($member_id, $group_id, 30, false);
        cleanup_member_timeouts();
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutTimeSubtraction()
    {
        $member_id = 3;
        $group_id = 4;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutDouble()
    {
        $group_id = 4;

        $member_id = 3;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        $member_id = 2;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(3)));
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(2)));
    }
}
