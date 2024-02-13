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
class emoticons_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_general_action');
        require_code('cns_general_action2');

        cns_make_emoticon('X:)', 'image/em.jpg', 1, 1, 0);

        $this->assertTrue('X:)' == $GLOBALS['FORUM_DB']->query_select_value('f_emoticons', 'e_code', ['e_code' => 'X:)']));
    }

    public function testEditemoticon()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_edit_emoticon('X:)', 'Z:D', 'images/smile.jpg', 2, 0, 0);

        $this->assertTrue('Z:D' == $GLOBALS['FORUM_DB']->query_select_value('f_emoticons', 'e_code', ['e_code' => 'Z:D']));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_emoticon('Z:D');

        parent::tearDown();
    }
}
