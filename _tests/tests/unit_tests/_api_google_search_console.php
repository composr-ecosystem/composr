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
class _api_google_search_console_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $this->load_key_options('google');
    }

    public function testGoogleSearchConsoleQuerying()
    {
        if (!addon_installed('google_search_console')) {
            $this->assertTrue(false, 'google_search_console addon is needed for this test');
            return;
        }

        require_code('oauth');
        $refresh_token = get_oauth_refresh_token('google_search_console');
        if ($refresh_token === null) {
            $this->assertTrue(false, 'We have set the API key etc we need, but oAuth is still needed to establish an API connection');
            return;
        }

        require_code('health_check');
        $this->run_health_check('API connections', 'Google Search Console', CHECK_CONTEXT__LIVE_SITE, true);
    }
}
