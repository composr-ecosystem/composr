<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class Tempcode_mistakes_test_set extends cms_test_case
{
    public function testIfPassedGuards()
    {
        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base() . '/themes');

        $regexp = '#\{\+START,IF_PASSED,(\w+)\}[^\{\}]*\{(?:(?!\1)\w)*\*?\}[^\{\}]*\{\+END\}#';

        $exceptions = [
            'default/templates/ATTACHMENT.tpl',
            'default/templates/FORM_SCREEN_INPUT_UPLOAD.tpl',
            'default/templates/FORM_SCREEN_INPUT_UPLOAD_MULTI.tpl',
        ];

        foreach ($files as $file) {
            if (in_array($file, $exceptions)) {
                continue;
            }

            if (substr($file, -4) == '.tpl') {
                $c = str_replace('{}', '', cms_file_get_contents_safe(get_file_base() . '/themes/' . $file, FILE_READ_LOCK | FILE_READ_BOM));
                $this->assertTrue(preg_match($regexp, $c) == 0, 'Found dodgy looking IF_PASSED situation in ' . $file);

                // By convention we have HTML coming out nicely formatted EXCEPT where there is Tempcode guarding an attribute at the start of the tag where we want the raw .tpl to work well in a code editor
                $matches = [];
                $this->assertTrue(preg_match('#<\w+\{\+#', $c, $matches) == 0, 'Code editors would find it hard to detect a tag start in ' . $file . ' (' . implode(' | ', $matches) . ')');
            }
        }
    }
}
