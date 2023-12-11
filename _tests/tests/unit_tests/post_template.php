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
class post_template_test_set extends cms_test_case
{
    public $post_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_general_action');
        require_code('cns_general_action2');

        $this->post_id = cns_make_post_template('Test Post', 'Testing', 'Code', 0);

        $this->assertTrue('Test Post' == $GLOBALS['FORUM_DB']->query_select_value('f_post_templates', 't_title', array('id' => $this->post_id)));
    }

    public function testEditpost_template()
    {
        cns_edit_post_template($this->post_id, 'Tested Post', 'Hello', 'Nothing', 1);

        $this->assertTrue('Tested Post' == $GLOBALS['FORUM_DB']->query_select_value('f_post_templates', 't_title', array('id' => $this->post_id)));
    }

    public function tearDown()
    {
        cns_delete_post_template($this->post_id);

        parent::tearDown();
    }
}
