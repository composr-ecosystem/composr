<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// ___api_twitter rather than _api_twitter because Twitter API will no longer be free and hence we will not be able to auto-test it so easily

/**
 * Composr test case class (unit testing).
 */
class ___api_twitter_test_set extends cms_test_case
{
    public function testTwitterApi()
    {
        $this->load_key_options('twitter_');

        require_code('health_check');
        $this->run_health_check('API connections', 'Twitter', CHECK_CONTEXT__TEST_SITE, true);
    }
}
