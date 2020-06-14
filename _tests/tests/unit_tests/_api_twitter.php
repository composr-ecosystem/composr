<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class _api_twitter_test_set extends cms_test_case
{
    public function testTwitterApi()
    {
        $this->load_key_options('twitter_');

        require_code('health_check');
        $this->run_health_check('API connections', 'Twitter', CHECK_CONTEXT__TEST_SITE, true);
    }
}
