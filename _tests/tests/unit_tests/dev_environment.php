<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        $this->assertTrue($GLOBALS['DEV_MODE'], 'Not running out of git or development mode disabled, therefore not all run-time checks enabled');
    }

    public function testcmsPHP()
    {
        $this->assertTrue(function_exists('ocp_mark_as_escaped'), 'Not running ocProducts PHP so XSS and type strictness errors won\'t be detected. If ocProducts PHP has been deprecated at this time (due to leverage of improvements in PHP7, and HHVM, and alternative XSS-avoidance strategies we\'ve since made), remove this test.');
    }
}
