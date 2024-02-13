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
class member_banning_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_members_action');
        require_code('cns_members_action2');
        require_lang('cns');
    }

    public function testBanUnban()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_ban_member(3);
        $this->assertTrue('1' == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_is_perm_banned', ['id' => 3]));

        cns_unban_member(3);
        $this->assertTrue('0' == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_is_perm_banned', ['id' => 3]));
    }
}
