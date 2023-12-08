<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class comcode_from_html_test_set extends cms_test_case
{
    public function testHTMLToComcode()
    {
        require_code('comcode_from_html');

        $tests = array(
            '<b>Hello</b>' => '[b]Hello[/b]',
            '<h2>Hello</h2>' => '[title="2"]Hello[/title]',
            '<p>Hello</p>' => 'Hello',
            '<font color="red">Hello</p>' => '[font color="red"]Hello[/font]',
            '<ul><li>Hello</li></ul>' => "[list]\n[*]Hello[/*]\n[/list]",
        );

        foreach ($tests as $html => $expected) {
            $got = semihtml_to_comcode($html, true);

            $ok = trim($got) == trim($expected);
            $this->assertTrue($ok);
            if (!$ok) {
                require_code('diff');
                echo '<code style="white-space: pre">' . diff_simple_2(trim($got), trim($expected), true) . '</code>';
            }
        }
    }
}
