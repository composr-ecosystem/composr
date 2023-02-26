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
class ticket_types_test_set extends cms_test_case
{
    protected $ticket_type_id;

    public function setUp()
    {
        parent::setUp();

        require_lang('tickets');
        require_code('tickets');
        require_code('tickets2');

        $this->ticket_type_id = add_ticket_type('platinum', 0, 0);
        $ticket_type_name = $GLOBALS['SITE_DB']->query_select_value('ticket_types', 'ticket_type_name', ['id' => $this->ticket_type_id]);
        $this->assertTrue('platinum' == get_translated_text($ticket_type_name));
    }

    public function testEditTicketType()
    {
        edit_ticket_type($this->ticket_type_id, 'gold', 0, 0);
        $ticket_type_name = $GLOBALS['SITE_DB']->query_select_value('ticket_types', 'ticket_type_name', ['id' => $this->ticket_type_id]);
        $this->assertTrue('gold' == get_translated_text($ticket_type_name));
    }

    public function tearDown()
    {
        delete_ticket_type($this->ticket_type_id);

        parent::tearDown();
    }
}
