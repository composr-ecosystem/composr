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
class ip_addresses_test_set extends cms_test_case
{
    public function testCIDR()
    {
        $this->assertTrue(ip_cidr_check('204.93.240.1', '204.93.240.0/24'));
        $this->assertTrue(!ip_cidr_check('204.93.241.1', '204.93.240.0/24'));
    }
}
