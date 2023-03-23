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

/**
 * Composr test case class (unit testing).
 */
class _api_moz_test_set extends cms_test_case
{
    public function testMozLinksApi()
    {
        $this->load_key_options('moz_');

        require_code('health_check');
        $this->run_health_check('API connections', 'Moz Links', CHECK_CONTEXT__TEST_SITE, true);
    }
}
