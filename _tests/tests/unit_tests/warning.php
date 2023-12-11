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
class warning_test_set extends cms_test_case
{
    public $warn_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_moderation');
        require_code('cns_moderation_action');
        require_code('cns_moderation_action2');

        $this->establish_admin_session();

        $this->warn_id = cns_make_warning(1, 'nothing', null, null, 1, null, null, 0, '', 0, 0, null);

        $this->assertTrue('nothing' == $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'w_explanation', array('id' => $this->warn_id)));
    }

    public function testEditWarning()
    {
        cns_edit_warning($this->warn_id, 'something', 1);

        $this->assertTrue('something' == $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'w_explanation', array('id' => $this->warn_id)));
    }

    public function tearDown()
    {
        cns_delete_warning($this->warn_id);

        parent::tearDown();
    }
}
