<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class wiki_test_set extends cms_test_case
{
    protected $id;

    public function setUp()
    {
        parent::setUp();

        require_code('wiki');
    }

    public function testAddWikipage()
    {
        require_code('permissions2');
        $this->id = wiki_add_page('test page', 'test description', 'test notes', 0);
        set_category_permissions_from_environment('wiki_page', strval($this->id), 'cms_wiki');

        // Check the page was actually created
        $this->assertTrue('test notes' == $GLOBALS['SITE_DB']->query_select_value('wiki_pages', 'notes', ['id' => $this->id]));
    }

    public function testEditWikiPage()
    {
        require_code('permissions2');
        set_category_permissions_from_environment('wiki_page', strval($this->id), 'cms_wiki');
        wiki_edit_page($this->id, 'title-edited', 'test description', 'notes_edited', 0, '', '');

        // Check the page was edited
        $this->assertTrue('notes_edited' == $GLOBALS['SITE_DB']->query_select_value('wiki_pages', 'notes', ['id' => $this->id]));
    }

    public function testDeleteWikipage()
    {
        // Delete Wiki+ page
        wiki_delete_page($this->id);
    }
}
