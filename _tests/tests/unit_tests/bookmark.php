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
class bookmark_test_set extends cms_test_case
{
    public $bookmark_id;

    public function setUp()
    {
        parent::setUp();

        require_code('bookmarks');

        $this->bookmark_id = add_bookmark(4, "xyz", "abc", "www.xyz.com");
        $this->assertTrue('abc' == $GLOBALS['SITE_DB']->query_select_value('bookmarks', 'b_title', array('id' => $this->bookmark_id)));
    }

    public function testEditBookmark()
    {
        edit_bookmark($this->bookmark_id, 4, "nnnnn", "www.xyz.com");
        $this->assertTrue('nnnnn' == $GLOBALS['SITE_DB']->query_select_value('bookmarks', 'b_title', array('id' => $this->bookmark_id)));
    }

    public function tearDown()
    {
        delete_bookmark($this->bookmark_id, 4);

        parent::tearDown();
    }
}
