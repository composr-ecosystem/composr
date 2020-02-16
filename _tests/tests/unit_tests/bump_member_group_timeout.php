<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class bump_member_group_timeout_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        push_query_limiting(false);

        $GLOBALS['FORUM_DB']->query_delete('f_group_member_timeouts');
        $GLOBALS['FORUM_DB']->query_delete('f_group_members');

        require_code('group_member_timeouts');
    }

    public function testMemberGroupTimeoutSecondary()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = 3;
        $group_id = 9;

        bump_member_group_timeout($member_id, $group_id, -10, false);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutPrimary()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = 3;
        $group_id = 9;

        bump_member_group_timeout($member_id, $group_id, -10, true);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutKickout()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = 3;
        $group_id = 9;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        cleanup_member_timeouts();
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutTimeAddition()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = 3;
        $group_id = 9;

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
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = 3;
        $group_id = 9;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));
    }

    public function testMemberGroupTimeoutDouble()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $group_id = 9;

        $member_id = 3;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        $member_id = 4;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(3)));
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(4)));
    }
}
