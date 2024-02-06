<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class microformats_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('lorem');

        require_once(get_file_base() . '/_tests/libs/mf_parse.php');

        $this->establish_admin_session();
    }

    public function testHCalendar()
    {
        if (($this->only !== null) && ($this->only != 'testHCalendar')) {
            return;
        }

        if (!function_exists('mb_convert_encoding')) {
            $this->assertTrue(false, 'PHP mbstring extension needed');
            return;
        }

        $tpl = render_screen_preview('calendar', 'tpl_preview__calendar_event_screen', 'CALENDAR_EVENT_SCREEN.tpl');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-event');
        $this->assertTrue(!empty($result['items'][0]['properties']['start']));
        $this->assertTrue(!empty($result['items'][0]['properties']['end']));
        $this->assertTrue(!@cms_empty_safe($result['items'][0]['properties']['category']));
        //$this->assertTrue(!@cms_empty_safe($result['items'][0]['properties']['summary'])); Validator cannot find, but exists in title
        $this->assertTrue(strpos($tpl->evaluate(), 'class="summary"') !== false);
        $this->assertTrue(!@cms_empty_safe($result['items'][0]['properties']['description']));
    }

    public function testHCalendarSideBlock()
    {
        if (($this->only !== null) && ($this->only != 'testHCalendarSideBlock')) {
            return;
        }

        if (!function_exists('mb_convert_encoding')) {
            $this->assertTrue(false, 'PHP mbstring extension needed');
            return;
        }

        $tpl = render_screen_preview('calendar', 'tpl_preview__block_side_calendar_listing', 'CALENDAR_EVENT_SCREEN.tpl');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-event');
        $this->assertTrue(!empty($result['items'][0]['properties']['start']));
        //$this->assertTrue(!@cms_empty_safe($result['items'][0]['properties']['summary'])); Validator cannot find, but exists in title
        $this->assertTrue(strpos($tpl->evaluate(), 'class="summary"') !== false);
    }

    public function testHCard()
    {
        if (($this->only !== null) && ($this->only != 'testHCard')) {
            return;
        }

        if (!function_exists('mb_convert_encoding')) {
            $this->assertTrue(false, 'PHP mbstring extension needed');
            return;
        }

        $tpl = render_screen_preview('core_cns', 'tpl_preview__cns_member_profile_screen', 'CNS_MEMBER_PROFILE_SCREEN.tpl');
        $result = $this->do_validation($tpl->evaluate());
        $this->assertTrue($result['items'][0]['type'][0] == 'h-card');
        $this->assertTrue(!empty($result['items'][0]['properties']['name']));
        $this->assertTrue(!empty($result['items'][0]['properties']['email']));
        $this->assertTrue(!empty($result['items'][0]['properties']['photo']));
        $this->assertTrue(!empty($result['items'][0]['properties']['bday']));
    }

    protected function do_validation($data)
    {
        cms_ini_set('ocproducts.type_strictness', '0');
        $output = Mf2\parse($data, 'https://waterpigs.co.uk/');
        cms_ini_set('ocproducts.type_strictness', '1');
        return $output;
    }
}
