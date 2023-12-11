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
class moderation_test_set extends cms_test_case
{
    public $mod_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_moderation_action');
        require_code('cns_moderation_action2');

        $this->mod_id = cns_make_multi_moderation('Test Moderation', 'Test', null, 0, 0, 0, '*', 'Nothing');

        $this->assertTrue('Test Moderation' == get_translated_text($GLOBALS['FORUM_DB']->query_select_value('f_multi_moderations', 'mm_name', array('id' => $this->mod_id)), $GLOBALS['FORUM_DB']));
    }

    public function testEditModeration()
    {
        cns_edit_multi_moderation($this->mod_id, 'Tested', 'Something', null, 0, 0, 0, '*', 'Hello');

        $this->assertTrue('Tested' == get_translated_text($GLOBALS['FORUM_DB']->query_select_value('f_multi_moderations', 'mm_name', array('id' => $this->mod_id)), $GLOBALS['FORUM_DB']));
    }

    public function tearDown()
    {
        cns_delete_multi_moderation($this->mod_id);

        parent::tearDown();
    }
}
