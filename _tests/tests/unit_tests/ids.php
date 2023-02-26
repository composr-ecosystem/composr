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
class ids_test_set extends cms_test_case
{
    public function testFixID()
    {
        $result = fix_id('hello__world12_34$A', true);
        $this->assertTrue($result == 'helloWorld1234A', 'Got ' . $result);
    }
}
