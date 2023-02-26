<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class warnings_test_set extends cms_test_case
{
    protected $warn_id;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_moderation');
        require_code('cns_moderation_action');
        require_code('cns_moderation_action2');

        $this->establish_admin_session();

        $this->warn_id = cns_make_warning(1, 'nothing', null, null, 1, null, null, 0, '', 0, 0, null);

        $this->assertTrue('nothing' == $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'w_explanation', ['id' => $this->warn_id]));
    }

    public function testEditWarning()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_edit_warning($this->warn_id, 'something', 1);

        $this->assertTrue('something' == $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'w_explanation', ['id' => $this->warn_id]));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_warning($this->warn_id);

        parent::tearDown();
    }
}
