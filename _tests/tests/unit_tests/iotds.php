<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class iotds_test_set extends cms_test_case
{
    protected $iotd_id;

    public function setUp()
    {
        parent::setUp();

        if (!addon_installed('iotds')) {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        require_code('iotds');
        require_code('iotds2');

        $this->iotd_id = add_iotd('https://duckduckgo.com/', 'Welcome', 'DuckDuckGo', 'images/duckduckgo.jpg', 0, 0, 0, 0, 'Notes ?', null, null, 0, null, 0, null);

        $this->assertTrue('https://duckduckgo.com/' == $GLOBALS['SITE_DB']->query_select_value('iotd', 'url', ['id' => $this->iotd_id]));
    }

    public function testEditIotd()
    {
        if (!addon_installed('iotds')) {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        edit_iotd($this->iotd_id, 'Thank you', 'Caption ?', 'images/yahoo.jpg', 'yahoo.com', 0, 0, 0, 'Notes');

        $this->assertTrue('yahoo.com' == $GLOBALS['SITE_DB']->query_select_value('iotd', 'url', ['id' => $this->iotd_id]));
    }

    public function tearDown()
    {
        if (!addon_installed('iotds')) {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        delete_iotd($this->iotd_id);

        parent::tearDown();
    }
}
