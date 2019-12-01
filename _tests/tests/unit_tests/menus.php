<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class menus_test_set extends cms_test_case
{
    protected $menu_id;

    public function setUp()
    {
        parent::setUp();

        require_code('menus2');

        $this->menu_id = add_menu_item('Test', 1, null, 'testing menu', 'https://duckduckgo.com/', 1, 'downloads', 0, 1, 'testing');

        $this->assertTrue('Test' == $GLOBALS['SITE_DB']->query_select_value('menu_items', 'i_menu', ['id' => $this->menu_id]));
    }

    public function testEditMenu()
    {
        edit_menu_item($this->menu_id, 'Service', 2, null, 'Serv', 'https://duckduckgo.com/', 0, 'catalogues', 1, 0, 'tested', '', 0);

        $this->assertTrue('Service' == $GLOBALS['SITE_DB']->query_select_value('menu_items', 'i_menu', ['id' => $this->menu_id]));
    }

    public function tearDown()
    {
        delete_menu_item($this->menu_id);

        parent::tearDown();
    }
}
