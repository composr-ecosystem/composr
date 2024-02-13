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
class multi_moderations_test_set extends cms_test_case
{
    protected $mod_id;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_moderation_action');
        require_code('cns_moderation_action2');

        $this->mod_id = cns_make_multi_moderation('Test Moderation', 'Test', null, 0, 0, '*', 'Nothing');

        $this->assertTrue('Test Moderation' == get_translated_text($GLOBALS['FORUM_DB']->query_select_value('f_multi_moderations', 'mm_name', ['id' => $this->mod_id]), $GLOBALS['FORUM_DB']));
    }

    public function testEditModeration()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_edit_multi_moderation($this->mod_id, 'Tested', 'Something', null, 0, 0, '*', 'Hello');

        $this->assertTrue('Tested' == get_translated_text($GLOBALS['FORUM_DB']->query_select_value('f_multi_moderations', 'mm_name', ['id' => $this->mod_id]), $GLOBALS['FORUM_DB']));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_multi_moderation($this->mod_id);

        parent::tearDown();
    }
}
