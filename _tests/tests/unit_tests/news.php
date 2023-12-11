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
class news_test_set extends cms_test_case
{
    public $news_id;

    public function setUp()
    {
        parent::setUp();

        require_code('news2');

        $this->news_id = add_news('Today', 'hiiiiiiiiiii', 'rolly', 1, 1, 1, 1, '', 'test article', 2, null, 1262671781, null, 0, null, null, '');
        $this->assertTrue('Today' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('news', 'title', array('id' => $this->news_id))));
    }

    public function testEditNews()
    {
        edit_news($this->news_id, 'Politics', 'teheyehehj ', 'rolly', 1, 1, 1, 1, 'yedd', 'test article 22222222', 2, null, '', '', '');

        $this->assertTrue('Politics' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('news', 'title', array('id' => $this->news_id))));
    }

    public function tearDown()
    {
        delete_news($this->news_id);

        parent::tearDown();
    }
}
