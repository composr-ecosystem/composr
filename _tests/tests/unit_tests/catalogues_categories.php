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
class catalogues_categories_test_set extends cms_test_case
{
    protected $cat_id;

    public function setUp()
    {
        parent::setUp();

        require_code('catalogues');
        require_code('catalogues2');

        $this->cat_id = actual_add_catalogue_category('Testing_category', 'Test_Cat', 'Testing_Cat', '', 1, '', 30, 60, null, null);

        $this->assertTrue('Testing_category' == $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'c_name', ['id' => $this->cat_id]));
    }

    public function testEditCatalogue_category()
    {
        actual_edit_catalogue_category($this->cat_id, 'Test_Cat', 'Cat_edit', 'Test', 1, '', '', '', 30, 60, 0);

        $this->assertTrue('Testing_category' == $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'c_name', ['id' => $this->cat_id]));
    }

    public function tearDown()
    {
        actual_delete_catalogue_category($this->cat_id, false);

        parent::tearDown();
    }
}
