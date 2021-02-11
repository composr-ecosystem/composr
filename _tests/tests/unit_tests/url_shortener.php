<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class url_shortener_test_set extends cms_test_case
{
    public function testURLShortening()
    {
        require_code('urls2');
        $short = shorten_url('https://example.com');
        $this->assertTrue($short == 'https://is.gd/jGamH3');
    }
}
