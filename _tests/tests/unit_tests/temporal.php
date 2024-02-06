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
class temporal_test_set extends cms_test_case
{
    public function testStrftime()
    {
        // Test simulated locales
        require_lang('dates');
        $this->assertTrue(cms_date('l', mktime(12, 0, 0, 2, 16, 2010)) == do_lang('TUESDAY'));
        $this->assertTrue(cms_date('F', mktime(12, 0, 0, 2, 16, 2010)) == do_lang('FEBRUARY'));

        // Test trimming
        $this->assertTrue(cms_date('j', mktime(12, 0, 0, 2, 9, 2010)) == '9');
    }
}
