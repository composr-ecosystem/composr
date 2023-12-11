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
class forum_test_set extends cms_test_case
{
    public $forum_id;
    public $access_mapping;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_forums_action');
        require_code('cns_forums_action2');
        require_lang('cns');

        $this->access_mapping = array(db_get_first_id() => 4);
        $this->forum_id = cns_make_forum('TestAdd', 'Test', db_get_first_id(), $this->access_mapping, db_get_first_id(), 1, 1, 0, '', '', '', 'last_post');

        $this->assertTrue('TestAdd' == $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_name', array('id' => $this->forum_id)));
    }

    public function testViewForum()
    {
        // Test the <title> contains "Test" which wil be in our forum name
        $this->get('forum:forumview:browse:' . strval($this->forum_id));
        $this->assertTitle(new PatternExpectation('/Test/'));
    }

    public function testEditForum()
    {
        cns_edit_forum($this->forum_id, 'TestEdit', 'Test', db_get_first_id(), db_get_first_id(), 1, 1, 0, '', '', '', 'last_post', 0, 0, false);
        $this->assertTrue('TestEdit' == $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_name', array('id' => $this->forum_id)));
    }

    public function tearDown()
    {
        cns_delete_forum($this->forum_id, $this->forum_id);

        parent::tearDown();
    }
}
