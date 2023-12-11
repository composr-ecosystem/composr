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
class chatroom_test_set extends cms_test_case
{
    public $chatroom_id;

    public function setUp()
    {
        parent::setUp();

        require_code('chat');
        require_code('chat2');

        $this->chatroom_id = add_chatroom('test_message', 'test_chat_room', 4, '', '2,3,4,5,6,7,8,9,10', '', '', 'EN', 0);
        $this->assertTrue('test_chat_room' == $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'room_name', array('id' => $this->chatroom_id)));
    }

    public function testEditChatroom()
    {
        edit_chatroom($this->chatroom_id, 'test message 1', 'test_chat_room1', 4, '', '2,3,4,5,6,7,8,9,10', '', '', 'EN');
        $this->assertTrue('test_chat_room1' == $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'room_name', array('id' => $this->chatroom_id)));
    }

    public function tearDown()
    {
        delete_chatroom($this->chatroom_id);

        parent::tearDown();
    }
}
