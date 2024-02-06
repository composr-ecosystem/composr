<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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

        $member_id = db_get_first_id() + 2;
        $group_id = db_get_first_id() + 3;

        bump_member_group_timeout($member_id, $group_id, -10, false);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));
    }

    public function testMemberGroupTimeoutPrimary()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = db_get_first_id() + 2;
        $group_id = db_get_first_id() + 3;

        bump_member_group_timeout($member_id, $group_id, -10, true);

        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));
    }

    public function testMemberGroupTimeoutKickout()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = db_get_first_id() + 2;
        $group_id = db_get_first_id() + 3;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        cleanup_member_timeouts();
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));
    }

    public function testMemberGroupTimeoutTimeAddition()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = db_get_first_id() + 2;
        $group_id = db_get_first_id() + 3;

        bump_member_group_timeout($member_id, $group_id, -10, false);
        bump_member_group_timeout($member_id, $group_id, 30, false);
        cleanup_member_timeouts();
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));
    }

    public function testMemberGroupTimeoutTimeSubtraction()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $member_id = db_get_first_id() + 2;
        $group_id = db_get_first_id() + 3;

        bump_member_group_timeout($member_id, $group_id, 10, false);
        bump_member_group_timeout($member_id, $group_id, -30, false);
        cleanup_member_timeouts();
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));
    }

    public function testMemberGroupTimeoutDouble()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $group_id = db_get_first_id() + 3;

        $member_id = db_get_first_id() + 2;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        $member_id = db_get_first_id() + 1;
        bump_member_group_timeout($member_id, $group_id, -10, false);
        $this->assertTrue(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id, false, true, true)));

        cleanup_member_timeouts();

        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(db_get_first_id() + 2, false, true, true)));
        $this->assertFalse(in_array($group_id, $GLOBALS['FORUM_DRIVER']->get_members_groups(db_get_first_id() + 1, false, true, true)));
    }
}
