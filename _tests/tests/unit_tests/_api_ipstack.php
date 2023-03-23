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
class _api_ipstack_test_set extends cms_test_case
{
    public function testIpStack()
    {
        $this->load_key_options('ipstack');
        $this->run_health_check('API connections', 'ipstack');
    }
}
