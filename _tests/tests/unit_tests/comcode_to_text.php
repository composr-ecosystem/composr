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
class comcode_to_text_test_set extends cms_test_case
{
    public function testComcodeToText()
    {
        $text = '
[list]
[*]A
[*]B
[*]C
[/list]

[title]header 1[/title]

under header 1

[title="2"]header 2[/title]

under header 2

[box="box title"]
box contents
[/box]

[b]test bold[/b]

[b]test italics[/b]

[highlight]test highlight[/highlight]

[staff_note]secret do not want[/staff_note]

[indent]blah
blah
blah[/indent]

[random a="Want"]1233[/random]

[abbr="Cascading Style Sheets"]CSS[/abbr]

{+START,IF_NON_EMPTY,foo}bar{+END}

{+START,IF_EMPTY,foo}bar{+END}

{$SITE_NAME}
';

        $got = strip_comcode($text);

        $expected = '
 - A
 - B
 - C

header 1
========

under header 1

header 2
--------

under header 2

box title
---------

box contents

**test bold**

**test italics**

***test highlight***

      blah
      blah
      blah

Want

CSS (Cascading Style Sheets)

bar

' . get_site_name();

        $ok = trim($got) == trim($expected);
        $this->assertTrue($ok);
        if (!$ok) {
            require_code('diff');
            echo '<p>Diff comparing what we expected to what we actually got:</p><code style="white-space: pre">' . diff_simple_2(trim($expected), trim($got), true) . '</code>';
        }
    }
}
