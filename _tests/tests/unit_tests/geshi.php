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
class geshi_test_set extends cms_test_case
{
    public function testGeshiWorks()
    {
        if (!addon_installed('geshi')) {
            $this->assertTrue(false, 'The geshi addon must be installed for this test to run');
            return;
        }

        // GeSHI is third party code and not maintained any more, so we need to ensure it keeps working

        require_code('geshi');

        $input = '
            <p class="foo">Bar</p>
';

        require_code('developer_tools');
        destrictify();

        $geshi = new GeSHi($input, 'html5');
        $geshi->set_header_type(GESHI_HEADER_DIV);
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        $output = $geshi->parse_code();

        restrictify();

        $this->assertTrue(strpos($output, '<a href="http://december.com/html/4/element/p.html">') !== false);
    }
}
