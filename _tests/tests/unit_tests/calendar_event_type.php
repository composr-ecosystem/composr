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
class calendar_event_type_test_set extends cms_test_case
{
    public $eventtype_id;

    public function setUp()
    {
        parent::setUp();

        require_code('calendar2');

        $this->eventtype_id = add_event_type('test_event_type', 'calendar/testtype', '');
        $this->assertTrue('test_event_type' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_types', 't_title', array('id' => $this->eventtype_id))));
    }

    public function testEditCalendarEventType()
    {
        edit_event_type($this->eventtype_id, 'test_event_type1', 'calendar/testtype1', '');
        $this->assertTrue('test_event_type1' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_types', 't_title', array('id' => $this->eventtype_id))));
    }

    public function tearDown()
    {
        delete_event_type($this->eventtype_id);

        parent::tearDown();
    }
}
