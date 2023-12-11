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
class forum_grouping_test_set extends cms_test_case
{
    public $forum_cat_id;
    public $access_mapping;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_forums_action');
        require_code('cns_forums_action2');
        require_lang('cns');

        $this->forum_cat_id = cns_make_forum_grouping('Test_cat', 'nothing', 1);

        $this->assertTrue('Test_cat' == $GLOBALS['FORUM_DB']->query_select_value('f_forum_groupings', 'c_title', array('id' => $this->forum_cat_id)));
    }

    public function testEditForumGrouping()
    {
        cns_edit_forum_grouping($this->forum_cat_id, 'New_title', 'somthing', 1);

        $this->assertTrue('New_title' == $GLOBALS['FORUM_DB']->query_select_value('f_forum_groupings', 'c_title', array('id' => $this->forum_cat_id)));
    }

    public function tearDown()
    {
        cns_delete_forum_grouping($this->forum_cat_id, 0);

        parent::tearDown();
    }
}
