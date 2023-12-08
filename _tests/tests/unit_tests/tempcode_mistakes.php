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
class Tempcode_mistakes_test_set extends cms_test_case
{
    public function testIfPassedGuards()
    {
        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base() . '/themes');

        $regexp = '#\{\+START,IF_PASSED,(\w+)\}[^\{\}]*\{(?:(?!\1)\w)*\*?\}[^\{\}]*\{\+END\}#';

        $exceptions = array(
            'default/templates/ATTACHMENT.tpl',
            'default/templates/FORM_SCREEN_INPUT_UPLOAD.tpl',
            'default/templates/FORM_SCREEN_INPUT_UPLOAD_MULTI.tpl',
        );

        foreach ($files as $file) {
            if (in_array($file, $exceptions)) {
                continue;
            }

            if (substr($file, -4) == '.tpl') {
                $c = file_get_contents(get_file_base() . '/themes/' . $file);
                $this->assertTrue(preg_match($regexp, $c) == 0, 'Found dodgy looking IF_PASSED situation in ' . $file);
            }
        }
    }
}
