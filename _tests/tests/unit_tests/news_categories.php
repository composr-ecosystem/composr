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
class news_categories_test_set extends cms_test_case
{
    protected $news_id;

    public function setUp()
    {
        parent::setUp();

        require_code('news2');

        $this->news_id = add_news_category('Today', 'news.gif', 'Headlines', null, null);

        $this->assertTrue('Today' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('news_categories', 'nc_title', ['id' => $this->news_id])));
    }

    public function testEditNewscategory()
    {
        edit_news_category($this->news_id, 'Politics', 'world.jpg', 'Around the world', null);

        $this->assertTrue('Politics' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('news_categories', 'nc_title', ['id' => $this->news_id])));
    }

    public function tearDown()
    {
        delete_news_category($this->news_id);

        parent::tearDown();
    }
}
