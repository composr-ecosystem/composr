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
class emoticon_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('cns_general_action');
        require_code('cns_general_action2');

        cns_make_emoticon('X:)', 'image/em.jpg', 1, 1, 0);

        $this->assertTrue('X:)' == $GLOBALS['FORUM_DB']->query_select_value('f_emoticons', 'e_code', array('e_code' => 'X:)')));
    }

    public function testEditemoticon()
    {
        cns_edit_emoticon('X:)', 'Z:D', 'images/smile.jpg', 2, 0, 0);

        $this->assertTrue('Z:D' == $GLOBALS['FORUM_DB']->query_select_value('f_emoticons', 'e_code', array('e_code' => 'Z:D')));
    }

    public function tearDown()
    {
        cns_delete_emoticon('Z:D');

        parent::tearDown();
    }
}
