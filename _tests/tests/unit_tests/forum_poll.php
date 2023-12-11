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
class forum_poll_test_set extends cms_test_case
{
    public $poll_id;
    public $topic_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_polls_action');
        require_code('cns_polls_action2');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');
        require_code('cns_topics');
        require_code('cns_forums');

        $this->establish_admin_session();

        $this->topic_id = cns_make_topic(db_get_first_id(), 'Test');

        $this->poll_id = cns_make_poll($this->topic_id, 'Who are you ?', 0, 0, 2, 4, 0, array('a', 'b', 'c'), true);

        $this->assertTrue('Who are you ?' == $GLOBALS['FORUM_DB']->query_select_value('f_polls', 'po_question', array('id' => $this->poll_id)));
    }

    public function testEditPoll()
    {
        cns_edit_poll($this->poll_id, 'Who am I?', 1, 1, 1, 4, 1, array('1', '2', '3'), 'nothing');

        $this->assertTrue('Who am I?' == $GLOBALS['FORUM_DB']->query_select_value('f_polls', 'po_question', array('id' => $this->poll_id)));
    }

    public function tearDown()
    {
        cns_delete_poll($this->poll_id, 'Simple');
        cns_delete_topic($this->topic_id);
        parent::tearDown();
    }
}
