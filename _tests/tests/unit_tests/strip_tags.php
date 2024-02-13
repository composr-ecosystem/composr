<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
        $this->assertTrue(strip_tags($x, $keep) == $expected);
        $got = cms_strip_tags($x, $keep, true);
        $this->assertTrue($got == $expected, 'Got ' . $got . ' but expected ' . $expected);

        $x = 'Hello <br /> <p>test</p><x>y</x>';
        $lose = '<x>';
        $expected = 'Hello <br /> <p>test</p>y';
        $got = cms_strip_tags($x, $lose, false);
        $this->assertTrue($got == $expected, 'Got ' . $got . ' but expected ' . $expected);

        // This is annoying, but it's how strip_tags in PHP works too
        $x = '<h1>This is a title</h1><p>This is some text</p>';
        $expected = 'This is a titleThis is some text';
        $this->assertTrue(strip_tags($x, $keep) == $expected);
        $got = cms_strip_tags($x);
        $this->assertTrue($got == $expected, 'Got ' . $got . ' but expected ' . $expected);

        // ... but strip_html is smarter
        $x = '<h1>This is a title</h1><p>This is some text</p>';
        $expected = 'This is a title This is some text';
        $got = strip_html($x);
        $this->assertTrue($got == $expected, 'Got ' . $got . ' but expected ' . $expected);
    }
}
