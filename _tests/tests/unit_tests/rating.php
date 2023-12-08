<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class rating_test_set extends cms_test_case
{
    public $event_id;

    public function setUp()
    {
        parent::setUp();

        require_code('calendar2');
        require_code('feedback');
        $this->event_id = add_calendar_event(8, 'none', null, 0, 'test_event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, null, 1, null, 1, 1, 1, 1, '', null, 0, null, null, null);
        if ('test_event' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'e_title', array('id' => $this->event_id)))) {
            $GLOBALS['SITE_DB']->query_insert('rating', array('rating_for_type' => 'events', 'rating_for_id' => $this->event_id, 'rating_member' => get_member(), 'rating_ip' => get_ip_address(), 'rating_time' => time(), 'rating' => 4));
        }
        $data = $GLOBALS['SITE_DB']->query_select('rating', array('rating '), array('rating_for_id' => $this->event_id, 'rating_member' => get_member()));
        $rating = $data[0]['rating'];
        $this->assertTrue(4 == $rating);
    }

    public function tearDown()
    {
        delete_calendar_event($this->event_id);
        $GLOBALS['SITE_DB']->query_delete('rating', array('rating_for_id' => $this->event_id, 'rating_member' => get_member()));

        parent::tearDown();
    }
}
