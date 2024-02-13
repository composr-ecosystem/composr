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
class comcode_to_text_test_set extends cms_test_case
{
    public function testComcodeToText()
    {
        $test_url = static_evaluate_tempcode(build_url(['page' => 'admin_actionlog'], 'adminzone', [], false, false, true));

        $text_sections = [
            '[list]
[*]A
[*]B
[*]C
[/list]',

            '[title]header 1[/title]',

            'under header 1',

            '[title="2"]header 2[/title]',

            'under header 2',

            '[box="box title"]
box contents
[/box]',

            '[b]test bold[/b]',

            '[b]test italics[/b]',

            '[highlight]test highlight[/highlight]',

            '[staff_note]secret do not want[/staff_note]',

            '[indent]blah
blah
blah[/indent]',

            '[random a="Want"]1233[/random]',

            '[abbr="Cascading Style Sheets"]CSS[/abbr]',

            '{+START,IF_NON_EMPTY,foo}bar{+END}',

            '{+START,IF_EMPTY,foo}bar{+END}',

            '{$SITE_NAME}',

            '[page="adminzone:admin_actionlog"]test[/page]',

            '[url="' . $test_url . '"]test[/url]',

            $test_url,
        ];
        $text = implode("\n\n", $text_sections);

        $got = strip_comcode($text);

        $expected_sections = [
            ' - A
 - B
 - C',

            'header 1
========',

            'under header 1',

            'header 2
--------',

            'under header 2',

            'box title
---------',

            'box contents',

            '**test bold**',

            '**test italics**',

            '***test highlight***',

            '      blah
      blah
      blah',

            'Want',

            'CSS (Cascading Style Sheets)',

            'bar',

            //'', Actually it will just be stripped entirely, no blank lines

            get_site_name(),

            'test (' . $test_url . ')',

            'test (' . $test_url . ')',

            $test_url,
        ];
        $expected = implode("\n\n", $expected_sections);

        $ok = trim($got) == trim($expected);

        $this->assertTrue($ok);
        if (!$ok) {
            require_code('diff');
            echo '<code style="white-space: pre">' . diff_simple_text(trim($got), trim($expected)) . '</code>';
        }
    }
}
