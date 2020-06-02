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
class diff_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('diff');
    }

    public function testSimpleDiff()
    {
        $result = diff_simple_text("a\nb\nc", "a\nb\nd");
        $this->assertTrue($result == '@@ -1,3 +1,3 @@<br /> a<br /> b<br /><span style="color: red">-c</span><br /><span style="color: green">+d</span><br /><br />');
    }

    public function test3WayDiff()
    {
        $result = diff_3way_text("a\nb\nc", "a\nb\nd", "b\nb\nc");
        $this->assertTrue($result == "b\nb\nd", 'Got ' . $result . '!');
    }
}
