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
class dev_environment_test_set extends cms_test_case
{
    public function testDevMode()
    {
        $this->assertTrue($GLOBALS['DEV_MODE'], 'Not running out of Git or development mode disabled, therefore not all run-time checks enabled');
    }

    public function testCMSPHP()
    {
        $this->assertTrue(function_exists('ocp_mark_as_escaped'), 'Not running ocProducts PHP so XSS and type strictness errors won\'t be detected');
    }
}
