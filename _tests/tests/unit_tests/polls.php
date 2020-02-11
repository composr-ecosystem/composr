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
class polls_test_set extends cms_test_case
{
    protected $poll_id;
    protected $topic_id;

    public function setUp()
    {
        parent::setUp();

        require_code('polls');
        require_code('polls2');

        $this->poll_id = add_poll('Who are you ?', 'a', 'b', 'c');

        $this->assertTrue('Who are you ?' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('poll', 'question', ['id' => $this->poll_id])));
    }

    public function testPollVote()
    {
        vote_in_poll($this->poll_id, 2);

        $poll_details = $GLOBALS['SITE_DB']->query_select('poll', ['*'], ['id' => $this->poll_id], '', 1);
        $this->assertTrue(array_key_exists(0, $poll_details));

        $this->assertTrue($poll_details[0]['votes2'] == 1);
    }

    public function testEditPoll()
    {
        edit_poll($this->poll_id, 'Who am I?', 'a', 'b', 'c', '', '', '', '', '', '', '', 3, 1, 1, 1, '');

        $this->assertTrue('Who am I?' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('poll', 'question', ['id' => $this->poll_id])));
    }

    public function tearDown()
    {
        delete_poll($this->poll_id);

        parent::tearDown();
    }
}
