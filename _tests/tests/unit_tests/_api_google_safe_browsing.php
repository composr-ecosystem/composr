<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _api_google_safe_browsing_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        set_option('hc_google_safe_browsing_api_enabled', '1');
        $this->load_key_options('google');
    }

    public function testMalwareScan()
    {
        require_code('health_check');
        $this->run_health_check('Security', 'Malware', CHECK_CONTEXT__LIVE_SITE, true);
    }
}
