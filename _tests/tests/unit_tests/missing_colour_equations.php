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
class missing_colour_equations_test_set extends cms_test_case
{
    public function testMissingColourEquations()
    {
        require_code('files2');

        $dont_check = [
            'commandr.css',
            'install.css',
            'widget_plupload.css',
            'widget_color.css',
            'widget_date.css',
            'widget_select2.css',
            'phpinfo.css',
            'jquery_ui.css',
            'mediaelementplayer.css',
            'skitter.css',
        ];

        $files = get_directory_contents(get_file_base() . '/themes/default/css', get_file_base() . '/themes/default/css', null, false, true, ['css']);
        foreach ($files as $path) {
            if (in_array(basename($path), $dont_check)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
            $matches = [];
            $count = preg_match_all('/^.+(\#[0-9A-Fa-f]{3,6})(.*)$/m', $c, $matches);
            for ($i = 0; $i < $count; $i++) {
                if (strpos($matches[0][$i], '{$') === false) { // If /*{$,hardcoded_ok}*/ is not on the line
                    $line = substr_count(substr($c, 0, strpos($c, $matches[0][$i])), "\n") + 1;
                    $this->assertTrue(false, 'Missing colour equation in ' . $path . ':' . strval($line) . ' for ' . $matches[1][$i]);
                }
            }
        }
    }
}
