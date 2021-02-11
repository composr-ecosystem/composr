<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class strip_tags_test_set extends cms_test_case
{
    public function testCmsStripTags()
    {
        $x = 'Hello <br /> <p>test</p><x>y</x>';
        $keep = '<x>';
        $expected = 'Hello  test<x>y</x>';

        //$this->assertTrue(strip_tags($x, $keep) == $expected);    Likely to stop working in PHP 7.3+. But known to pass.
        $this->assertTrue(cms_strip_tags($x, $keep, true) == $expected);

        $x = 'Hello <br /> <p>test</p><x>y</x>';
        $lose = '<x>';
        $expected = 'Hello <br /> <p>test</p>y';

        $this->assertTrue(cms_strip_tags($x, $lose, false) == $expected);
    }
}
