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
class user_banunban_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('cns_members_action');
        require_code('cns_members_action2');
        require_lang('cns');

        cns_ban_member(3);

        $this->assertTrue(1 == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_is_perm_banned', array('id' => 3)));
    }

    public function testEdituser_banunban()
    {
        cns_unban_member(3);

        $this->assertTrue(0 == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_is_perm_banned', array('id' => 3)));
    }
}
