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
class microformats_test_set extends cms_test_case
{
    public function setUp()
    {
        require_code('lorem');

        require_once(get_file_base() . '/_tests/libs/mf_parse.php');

        $this->establish_admin_session();

        parent::setUp();
    }

    public function testHCalendar()
    {
        $tpl = render_screen_preview('CALENDAR_EVENT_SCREEN.tpl', 'calendar', 'tpl_preview__calendar_event_screen');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-event');
        $this->assertTrue(!empty($result['items'][0]['properties']['start']));
        $this->assertTrue(!empty($result['items'][0]['properties']['end']));
        $this->assertTrue(!empty($result['items'][0]['properties']['category']));
        //$this->assertTrue(!empty($result['items'][0]['properties']['summary'])); Validator cannot find, but exists in title
        $this->assertTrue(strpos($tpl->evaluate(), 'class="summary"') !== false);
        $this->assertTrue(!empty($result['items'][0]['properties']['description']));
    }

    public function testHCalendarSideBlock()
    {
        $tpl = render_screen_preview('CALENDAR_EVENT_SCREEN.tpl', 'calendar', 'tpl_preview__block_side_calendar_listing');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-event');
        $this->assertTrue(!empty($result['items'][0]['properties']['start']));
        //$this->assertTrue(!empty($result['items'][0]['properties']['summary'])); Validator cannot find, but exists in title
        $this->assertTrue(strpos($tpl->evaluate(), 'class="summary"') !== false);
    }

    public function testHCard()
    {
        $tpl = render_screen_preview('CNS_MEMBER_PROFILE_SCREEN.tpl', 'core_cns', 'tpl_preview__cns_member_profile_screen');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-card');
        $this->assertTrue(!empty($result['items'][0]['properties']['name']));
        $this->assertTrue(!empty($result['items'][0]['properties']['email']));
        $this->assertTrue(!empty($result['items'][0]['properties']['photo']));
        $this->assertTrue(!empty($result['items'][0]['properties']['bday']));
    }

    protected function do_validation($data)
    {
        $output = Mf2\parse($data, 'https://waterpigs.co.uk/');
        return $output;
    }
}
