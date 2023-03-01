<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class forums_test_set extends cms_test_case
{
    protected $forum_id;
    protected $access_mapping;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_forums_action');
        require_code('cns_forums_action2');
        require_lang('cns');

        $this->access_mapping = [db_get_first_id() => 4];
        $this->forum_id = cns_make_forum('TestAdd', 'Test', db_get_first_id(), $this->access_mapping, db_get_first_id(), 1, 1, 0, '', '', '', 'last_post', 0, 0, '', '', '', null, '', '', '', 'post_as_guest', 1, '');

        $this->assertTrue('TestAdd' == $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_name', ['id' => $this->forum_id]));
    }

    public function testViewForum()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        // Test the <title> contains "Test" which wil be in our forum name
        $page_link = 'forum:forumview:browse:' . strval($this->forum_id);
        $contents = $this->get($page_link);
        $this->assertTrue(preg_match('#<title>.*Test.*</title>#', $contents) != 0, 'Could not get expected Title on ' . $page_link);
    }

    public function testEditForum()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_edit_forum($this->forum_id, 'TestEdit', 'Test', db_get_first_id(), db_get_first_id(), 1, 1, 0, '', '', '', 'last_post', 0, 0, '', '', '', null, '', '', '', 'post_as_guest', 1, false, '');
        $this->assertTrue('TestEdit' == $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_name', ['id' => $this->forum_id]));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_forum($this->forum_id, $this->forum_id);

        parent::tearDown();
    }
}
