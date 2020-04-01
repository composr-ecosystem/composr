<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class browser_upgrade_suggest_test_set extends cms_test_case
{
    public function testBrowserLib()
    {
        if (!addon_installed('browser_detect')) {
            $this->assertTrue(false, 'The browser_detect addon must be installed for this test to run');
            return;
        }

        require_code('browser_detect');

        $browser = new Browser('Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1');
        $this->assertTrue($browser->getBrowser() == Browser::BROWSER_FIREFOX);
        $this->assertTrue($browser->getVersion() == 15);
    }
}
