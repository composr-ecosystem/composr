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
class quiz_test_set extends cms_test_case
{
    public $quiz_id;

    public function setUp()
    {
        parent::setUp();

        require_code('quiz2');

        $this->quiz_id = add_quiz('Quiz1', 15, 'Begin', 'End', '', 'somethng', 60, time(), null, 1, 0, 'TEST', 1, 'Questions', null, 0, null);

        $this->assertTrue('Quiz1' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('quizzes', 'q_name', array('id' => $this->quiz_id))));
    }

    public function testEditQuiz()
    {
        edit_quiz($this->quiz_id, 'Quiz2', 10, 'Go', 'Stop', '', 'Nothing', 50, time(), null, 3, 0, 'TEST', 1, 'Questions', 'Nothing', '', 0, null);

        $this->assertTrue('Quiz2' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('quizzes', 'q_name', array('id' => $this->quiz_id))));
    }

    public function tearDown()
    {
        delete_quiz($this->quiz_id);
        parent::tearDown();
    }
}
