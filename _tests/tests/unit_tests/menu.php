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
class menu_test_set extends cms_test_case
{
    public $menu_id;

    public function setUp()
    {
        parent::setUp();

        require_code('menus');
        require_code('menus2');

        $this->menu_id = add_menu_item('Test', 1, null, 'testing menu', 'http://www.example.com', 1, 'downloads', 0, 1, 'testing');

        $this->assertTrue('Test' == $GLOBALS['SITE_DB']->query_select_value('menu_items', 'i_menu', array('id' => $this->menu_id)));
    }

    public function testEditMenu()
    {
        edit_menu_item($this->menu_id, 'Service', 2, null, 'Serv', 'http://www.google.com', 0, 'catalogues', 1, 0, 'tested', '', 0);

        $this->assertTrue('Service' == $GLOBALS['SITE_DB']->query_select_value('menu_items', 'i_menu', array('id' => $this->menu_id)));
    }

    public function testURLRendering()
    {
        $urls = array();

        $branch = array(
            'title' => 'test',
            'content_type' => 'stored_branch',
            'content_id' => null,
            'modifiers' => array(),
            'only_on_page' => '',
            'extra_meta' => array(
            ),
            'has_possible_children' => false,
            'children' => array(),
        );

        $tests = array();
        $tests[] = array(
            'url' => static_evaluate_tempcode(build_url(array('page' => 'rules'), '')),
            'page_link' => '',
        );
        $tests[] = array(
            'url' => '',
            'page_link' => ':rules',
        );

        foreach ($tests as $test) {
            $result = _render_menu_branch($branch + $test, 'test', db_get_first_id(), 0, 'tree', false, array($branch), false);

            $url = $result[0]['URL']->evaluate();
            $urls[] = $url;
            $this->assertTrue($url == $urls[0], $url . ' vs ' . $urls[0]);
        }
    }

    public function tearDown()
    {
        delete_menu_item($this->menu_id);

        parent::tearDown();
    }
}
